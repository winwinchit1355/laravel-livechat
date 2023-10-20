<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use App\Events\PusherBroadcast;
use App\Models\ReadReceipt;
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
            $usersWithMessages=User::join('chat_messages','users.id','=','chat_messages.sender_id')
                ->select('users.*')
                ->orderBy('users.name')
                ->distinct()
                ->get();

            $users=User::leftJoin('chat_messages','users.id','=','chat_messages.receiver_id')
                ->select('users.*')
                ->where('users.id','<>',auth()->user()->id)
                ->whereNotIn('users.id',$usersWithMessages->pluck('id'))
                ->orderBy('users.name')
                ->get();

            $receiver_id=Auth::id();
            $oldMessages=ChatMessage::where('sender_id',auth()->user()->id)
                ->where('receiver_id',auth()->user()->id)
                ->get();
            return view('pusher.admin-chat',compact('users','receiver_id','oldMessages','usersWithMessages'));
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
            if($request->has('message') && $request->message != null)
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
                'receiver_id'=>$message->receiver_id,
                'message_id'=>$message->id,
            ];
            broadcast(new PusherBroadcast($request->get('message'),$filePath,$message->sender_id,$message->receiver_id,$message->id))->toOthers();
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
            'receiver_id'=>$request->get('receiver_id'),
            'message_id'=>$request->get('message_id'),
            'filePath'=>$request->get('filePath')
        ];
        return view('pusher.receive',$data);
    }
    public function getUserMessages($user_id)
    {
        $receiver_id=\Crypt::decrypt($user_id);
        $usersWithMessages=User::join('chat_messages','users.id','=','chat_messages.sender_id')
                ->select('users.*')
                ->orderBy('users.name')
                ->distinct()
                ->get();

        $users=User::leftJoin('chat_messages','users.id','=','chat_messages.receiver_id')
                ->select('users.*')
                ->where('users.id','<>',auth()->user()->id)
                ->whereNotIn('users.id',$usersWithMessages->pluck('id'))
                ->orderBy('users.name')
                ->get();
        $oldMessages = ChatMessage::where(function ($query) use ($receiver_id) {
                $query->where('sender_id', Auth::id());
                $query->where('receiver_id', $receiver_id);
            })->orWhere(function ($query) use ($receiver_id) {
                $query->where('sender_id', $receiver_id);
                $query->where('receiver_id', Auth::id());
            })->get();
        return view('pusher.admin-chat',compact('users','oldMessages','receiver_id','usersWithMessages'));
    }
    public function addReadReceipt(Request $request)
    {
        $messageId=null;
        if($request->message_id != null)
        {
            $messageId=\Crypt::decrypt($request->message_id);
            // $messageId=$request->message_id;
            try {
                // Start a database transaction
                DB::beginTransaction();
                ReadReceipt::where([
                    'user_id' => Auth::id(),
                    'message_id' => $messageId,
                ])->lockForUpdate()->first(); //to prevent concurrent request
                $read = ReadReceipt::firstOrCreate(
                    [
                        'user_id' => Auth::id(),
                        'message_id' => $messageId,
                    ],
                    [
                        'seen_at' => now(), // Use Laravel's now() helper to set the current timestamp
                    ]
                );

                // Commit the transaction
                DB::commit();
                return response()->json(['message_id'=>$read]);
            } catch (\Exception $e) {
                // Handle any exceptions that may occur during the transaction
                // You can roll back the transaction here if needed
                DB::rollBack();

                // Log or handle the exception as appropriate for your application
            }
        }
    }
}
