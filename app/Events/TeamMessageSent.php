<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('team.' . $this->message->team_id);
    }

    public function broadcastWith()
    {
        $this->message->load('sender.githubAccount');

        return [
            'id' => $this->message->id,
            'message' => $this->message->message,
            'team_id' => $this->message->team_id,
            'sender_id' => $this->message->sender_id,
            'created_at' => $this->message->created_at->toDateTimeString(),
            'is_sent_by_me' => $this->message->is_sent_by_me,
            'sender_avatar' => $this->message->sender_avatar,
            'sender' => [
                'id' => $this->message->sender->id,
                'name' => $this->message->sender->name,
                'email' => $this->message->sender->email,
                'github_avatar' => $this->message->sender->githubAccount?->avatar,
            ],
        ];
    }

//    public function broadcastWith()
//    {
//        return [
//            'id' => $this->message->id,
//            'message' => $this->message->message,
//            'team_id' => $this->message->team_id,
//            'sender_id' => $this->message->sender_id,
//            'created_at' => $this->message->created_at,
//
//            'is_sent_by_me' => $this->message->is_sent_by_me,
//            'sender_avatar' => $this->message->sender_avatar,
//
//            'sender' => [
//                'id' => $this->message->sender->id,
//                'name' => $this->message->sender->name,
//                'email' => $this->message->sender->email,
//            ],
//        ];
//    }


}
