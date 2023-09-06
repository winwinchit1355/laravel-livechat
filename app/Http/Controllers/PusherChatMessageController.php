<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use App\Events\PusherBroadcast;

class PusherChatMessageController extends Controller
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
        return view('pusher.admin-chat',compact('users','messages'));
    }
    public function store(Request $request)
    {
        //
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
        return view('pusher.chat',compact('users','messages'));
    }
    public function broadcast(Request $request)
    {
        broadcast(new PusherBroadcast($request->get('message')))->toOthers();
        return view('pusher.broadcast',['message'=>$request->get('message')]);
    }
    public function receive(Request $request)
    {
        return view('pusher.receive',['message'=>$request->get('message')]);
    }
}
