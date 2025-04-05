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

    public $teamId;
    public $messageData;
    public $userData;

    public function __construct($teamId, $message, $user)
    {
        $this->teamId = $teamId;

        $this->messageData = [
            'id' => $message->id,
            'content' => $message->message,
            'created_at' => $message->created_at->toDateTimeString(),
        ];

        $this->userData = [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => optional($user->githubAccount)->avatar
                ? asset(Storage::url($user->githubAccount->avatar))
                : null,
        ];
    }

    public function broadcastOn()
    {
        return new Channel('team.' . $this->teamId);
    }

    public function broadcastAs()
    {
        return 'team.message.sent';
    }
}
