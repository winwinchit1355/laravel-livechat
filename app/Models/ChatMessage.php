<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ChatMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public static function chatLists($refer_id=null) {
        $sendMessages = ChatMessage::where('sender_id', Auth::id())
                        ->select(['id', 'sender_id as auth_id', 'receiver_id as refer_id', 'message', 'created_at'])
                        ->get();
        $sendMessages->map(function($sendMessage) {
            $sendMessage['status'] = 'send';
            return $sendMessage;
        });
        $receiveMessages = ChatMessage::where('receiver_id', Auth::id())
                            ->select(['id', 'receiver_id as auth_id', 'sender_id as refer_id', 'message', 'created_at'])
                            ->get();
        $receiveMessages->map(function($receiveMessage) {
            $receiveMessage['status'] = 'receive';
            return $receiveMessage;
        });
        $messages = $sendMessages->merge($receiveMessages);
        if ($refer_id) {
            $messages = $messages->where('refer_id', $refer_id);
        }
        return $messages->sortBy('created_at');
    }

    public static function getUSerName($id)
    {
        return User::find($id) ? User::find($id)->name : '';
    }
}
