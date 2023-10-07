<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use App\Events\PusherBroadcast;
use Illuminate\Support\Facades\DB;

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

            return view('pusher.admin-chat-default',compact('users'));
        }
        else{
            $admin=User::where('role','admin')->first();
            $oldMessages=ChatMessage::where('sender_id',auth()->user()->id)
                ->orWhere('receiver_id',auth()->user()->id)
                ->get();
            $receiver_id=$admin->id;
            return view('pusher.client-chat',compact('oldMessages','receiver_id'));
        }
    }
    // public function fetchMessages()
    // {
    //     return ChatMessage::with('user')->get();
    // }
    public function broadcast(Request $request)
    {

        $receiver_id=\Crypt::decrypt($request->receiver_id);
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
                $message->receiver_id=$receiver_id;
                $message->file=$filePath;
                $message->save();
            }
            if($request->has('message'))
            {
                $message=new ChatMessage();
                $message->sender_id=Auth::id();
                $message->receiver_id=$receiver_id;
                $message->message=$request->message;
                $message->save();
            }
            $data=[
                'message'=>$request->get('message'),
                'filePath'=> $filePath,
                'sender_id'=>$message->sender_id,
                'receiver_id'=>$message->receiver_id
            ];
            broadcast(new PusherBroadcast($request->get('message'),$filePath,$message->sender_id,$message->receiver_id))->toOthers();
            DB::commit();

            return view('pusher.broadcast',$data);
        }catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }


    }
    public function receive(Request $request)
    {
        $data=[
            'message'=>$request->get('message'),
            'sender_id'=>$request->get('sender_id'),
            'filePath'=>$request->get('filePath')
        ];
        return view('pusher.receive',$data);
    }
    public function getUserMessages($user_id)
    {
        $receiver_id=\Crypt::decrypt($user_id);
        $users=User::where('id','<>',auth()->user()->id)->get();
        $oldMessages = ChatMessage::where(function ($query) use ($receiver_id) {
                $query->where('sender_id', Auth::id());
                $query->where('receiver_id', $receiver_id);
            })->orWhere(function ($query) use ($receiver_id) {
                $query->where('sender_id', $receiver_id);
                $query->where('receiver_id', Auth::id());
            })->get();
        return view('pusher.admin-chat',compact('users','oldMessages','receiver_id'));
    }
}
