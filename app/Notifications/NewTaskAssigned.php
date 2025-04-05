<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTaskAssigned extends Notification
{
    use Queueable;

    public $task;
    public function __construct($task)
    {
        $this->task = $task;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => "New Task Assigned",
            'message' => "A new task '{$this->task->title}' has been assigned to you.",
            'task_id' => $this->task->id,
        ];
    }
}
