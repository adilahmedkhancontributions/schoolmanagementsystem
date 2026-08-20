<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TimetableChanged extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public string $changedBy,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title)
            ->greeting("Hello {$notifiable->name},")
            ->line($this->message)
            ->line("Changed by: {$this->changedBy}");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'timetable_change',
            'title' => $this->title,
            'body' => $this->message,
            'changed_by' => $this->changedBy,
        ];
    }
}
