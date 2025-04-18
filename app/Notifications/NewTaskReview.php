<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTaskReview extends Notification
{
    use Queueable;

    public $review, $task;
    public function __construct($review, $task)
    {
        $this->review = $review;
        $this->task = $task;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public  function toDatabase($notifiable): array
    {
        return [
            'review_id' => $this->review->id,
            'message' => 'The task "' . $this->task->title . '" has been completed by ' . auth()->user()->name,
        ];
    }
}
