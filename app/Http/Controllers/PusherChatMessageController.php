<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use App\Events\PusherBroadcast;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Mime\Message;

use Illuminate\Support\Facades\Auth;
use function Symfony\Component\HttpFoundation\Session\Storage\Handler\rollback;
use function Symfony\Component\HttpFoundation\Session\Storage\Handler\beginTransaction;

class PusherChatMessageController extends Controller
{
    public function pusherChat()
    {
        if(Auth::user()->role == 'admin')
        {
            $users=User::where('id','<>',auth()->user()->id)->get();
            $messages=ChatMessage::where('sender_id',auth()->user()->id)
                ->where(function($query) use($receiver_id){
                    $query->where('sender_id',auth()->user()->id);
                    $query->where('receiver_id',$receiver_id);
                })
                ->orWhere(function($query) use($receiver_id){
                    $query->where('sender_id',$receiver_id);
                    $query->where('receiver_id',auth()->user()->id);
                })
                ->get();
            return view('pusher.admin-chat',compact('users','messages'));
        }
        else{
            $oldMessages=ChatMessage::where('sender_id',auth()->user()->id)
                ->orWhere('receiver_id',auth()->user()->id)
                ->get();
            return view('pusher.client-chat',compact('oldMessages'));
        }
    }
    // public function adminChat()
    // {
    //     $receiver_id=1;
    //     if(auth()->user()->id==1){
    //         $receiver_id=2;
    //     }

    //     $users=User::where('id','<>',auth()->user()->id)->get();
    //     $messages=ChatMessage::where('sender_id',auth()->user()->id)
    //         ->where(function($query) use($receiver_id){
    //             $query->where('sender_id',auth()->user()->id);
    //             $query->where('receiver_id',$receiver_id);
    //         })
    //         ->orWhere(function($query) use($receiver_id){
    //             $query->where('sender_id',$receiver_id);
    //             $query->where('receiver_id',auth()->user()->id);
    //         })
    //         ->get();
    //     return view('pusher.admin-chat',compact('users','messages'));
    // }
    public function store(Request $request)
    {
        //
    }
    public function fetchMessages()
    {
        return ChatMessage::with('user')->get();
    }
    // public function clientChat(Request $request)
    // {
    //     $receiver_id=1;
    //     if(auth()->user()->id==1){
    //         $receiver_id=2;
    //     }

    //     $users=User::where('id','<>',auth()->user()->id)->get();
    //     $messages=ChatMessage::where('sender_id',auth()->user()->id)
    //         ->where(function($query) use($receiver_id){
    //             $query->where('sender_id',auth()->user()->id);
    //             $query->where('receiver_id',$receiver_id);
    //         })
    //         ->orWhere(function($query) use($receiver_id){
    //             $query->where('sender_id',$receiver_id);
    //             $query->where('receiver_id',auth()->user()->id);
    //         })
    //         ->get();
    //     return view('pusher.chat',compact('users','messages'));
    // }
    public function broadcast(Request $request)
    {
        $admin=User::where('role','admin')->first();
        $filePath='';
        try{
            DB::beginTransaction();
            if($request->hasFile('file')){
                $file=$request->file('file');
                $fileName=time().'.'.$file->extension();
                $request->file->move(public_path('uploads'), $fileName);
                $filePath='uploads/'.$fileName;

                $message=new ChatMessage();
                $message->sender_id=Auth::id();
                $message->receiver_id=$admin->id;
                $message->file=$filePath;
                $message->save();
            }
            if($request->has('message'))
            {
                $message=new ChatMessage();
                $message->sender_id=Auth::id();
                $message->receiver_id=$admin->id;
                $message->message=$request->message;
                $message->save();
            }

            broadcast(new PusherBroadcast($request->get('message'),$request->get('files')))->toOthers();
            DB::commit();
            return view('pusher.broadcast',['message'=>$request->get('message'),'filePath'=> $filePath]);
        }catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }


    }
    public function receive(Request $request)
    {
        return view('pusher.receive',['message'=>$request->get('message')]);
    }
}
