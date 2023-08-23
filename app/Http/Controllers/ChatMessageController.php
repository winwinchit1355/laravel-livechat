<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{

    public function index()
    {
        $receiver_id=1;
        if(auth()->user()->id==1){
            $receiver_id=2;
        }

        $users=User::where('id','<>',auth()->user()->id)->get();
        $messages=ChatMessage::where('sender_id',auth()->user()->id)
            ->where('receiver_id',$receiver_id)
            ->get();
        return view('chat',compact('users','messages'));
    }
    public function store(Request $request)
    {
        $receiver_id=1;
        if(auth()->user()->id==1){
            $receiver_id=2;
        }

        $message=new ChatMessage;
        $message->sender_id=auth()->user()->id;
        $message->receiver_id=$request->receiver_id;
        $message->message=$request->message;
        $message->save();

        $messages=ChatMessage::where('sender_id',auth()->user()->id)
            ->where('receiver_id',$this->receiver_id)
            ->get();
        return redirect()->back()->with('messages',$messages);
    }
}
