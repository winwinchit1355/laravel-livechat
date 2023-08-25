<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $messages = ChatMessage::chatLists();
        $chatUsers = User::where('id', '<>', Auth::id())->get();
        $messages->groupBy('refer_id');

        return view('home', compact('messages', 'chatUsers'));
    }

    public function chatpage()
    {
        $refer_id = request()->query('refer_id');
        $messages = ChatMessage::chatLists(request()->query('refer_id'));
        $chatUser = ChatMessage::getUSerName(request()->query('refer_id'));

        return view('chatpage', compact('refer_id', 'messages', 'chatUser'));
    }

    public function chat(Request $request)
    {
        ChatMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->refer_id,
            'message' => $request->message
        ]);

        return redirect()->back();
    }
}
