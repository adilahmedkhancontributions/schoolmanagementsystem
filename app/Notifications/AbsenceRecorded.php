<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbsenceRecorded extends Notification
{
    use Queueable;

    public function __construct(
        public string $studentName,
        public string $date,
        public string $status,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Attendance Alert: {$this->studentName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("{$this->studentName} was marked as {$this->status} on {$this->date}.")
            ->line('If this is an error, please contact the school administration.')
            ->action('View Attendance', route('student.attendance'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'absence_recorded',
            'title' => 'Attendance Alert',
            'body' => "{$this->studentName} was {$this->status} on {$this->date}.",
        ];
    }
}
