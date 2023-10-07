<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadReceipt extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'message_id',
        'seen_at',
    ];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function message()
    {
        return $this->belongsTo(ChatMessage::class,'message_id');
    }
}
