<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReportAdded extends Notification
{
    use Queueable;

    public $report, $task, $developer;
    public function __construct($report, $task, $developer)
    {
        $this->report = $report;
        $this->task = $task;
        $this->developer = $developer;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'report_id' => $this->report->id,
            'message' => "New report added for task {$this->task->title} by {$this->developer->name}",
        ];
    }
}
