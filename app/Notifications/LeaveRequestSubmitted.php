<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRequestSubmitted extends Notification
{
    use Queueable;

    public function __construct(
        public string $requesterName,
        public string $type,
        public string $fromDate,
        public string $toDate,
        public string $reason,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New {$this->type} Leave Request")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new {$this->type} leave request has been submitted.")
            ->line("From: {$this->fromDate} To: {$this->toDate}")
            ->line("Reason: {$this->reason}")
            ->line("Submitted by: {$this->requesterName}")
            ->action('Review Requests', route('school-admin.leave'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'leave_request',
            'title' => 'New Leave Request',
            'body' => "{$this->requesterName} requested {$this->type} leave from {$this->fromDate} to {$this->toDate}.",
        ];
    }
}
