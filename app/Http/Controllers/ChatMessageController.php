<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Events\ChatEvent;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use App\Events\PusherBroadcast;

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
            ->where(function($query) use($receiver_id){
                $query->where('sender_id',auth()->user()->id);
                $query->where('receiver_id',$receiver_id);
            })
            ->orWhere(function($query) use($receiver_id){
                $query->where('sender_id',$receiver_id);
                $query->where('receiver_id',auth()->user()->id);
            })
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
        $message->receiver_id=$receiver_id;
        $message->message=$request->message;
        $message->save();
        event(new ChatEvent(auth()->user(),'hello world'));
        // event(new ChatEvent(auth()->user(), $message->message));

        $messages=ChatMessage::where('sender_id',auth()->user()->id)
            ->where('receiver_id',$receiver_id)
            ->get();
        return response()->json(['success' => true,'username'=>$request->username,'message'=>$messages]);
    }
    public function fetchMessages()
    {
        return ChatMessage::with('user')->get();
    }
    public function all(Request $request)
    {
        $receiver_id=1;
        if(auth()->user()->id==1){
            $receiver_id=2;
        }

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
        return view('chat2',compact('users','messages'));
    }
    public function broadcast(Request $request)
    {
        broadcast(new PusherBroadcast($request->get('message')))->toOthers();
        return view('broadcast',['message'=>$request->get('message')]);
    }
    public function receive(Request $request)
    {
        return view('receive',['message'=>$request->get('message')]);
    }
}
