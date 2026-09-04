<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRequestUpdated extends Notification
{
    use Queueable;

    public function __construct(
        public string $status,
        public string $fromDate,
        public string $toDate,
        public ?string $adminNote = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Leave Request {$this->status}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your leave request ({$this->fromDate} to {$this->toDate}) has been {$this->status}.");

        if ($this->adminNote) {
            $mail->line("Note from admin: {$this->adminNote}");
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'leave_updated',
            'title' => 'Leave Request ' . ucfirst($this->status),
            'body' => "Your leave request ({$this->fromDate} to {$this->toDate}) has been {$this->status}.",
        ];
    }
}
