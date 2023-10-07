<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PusherBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $filePath;
    public $sender_id;
    public $receiver_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message,$filePath,$sender_id,$receiver_id)
    {
        $this->message=$message;
        $this->filePath=$filePath;
        $this->sender_id=$sender_id;
        $this->receiver_id=$receiver_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['public' . $this->receiver_id ];
        // return new PrivateChannel('channel-name');
    }
    public function broadcastAs()
    {
        // Specify the custom name for the broadcasted event
        return 'chat';
    }
}
