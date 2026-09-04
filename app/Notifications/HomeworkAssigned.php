<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HomeworkAssigned extends Notification
{
    use Queueable;

    public function __construct(
        public string $subject,
        public string $title,
        public string $dueDate,
        public string $className,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Homework: {$this->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("New homework has been assigned in {$this->subject} for {$this->className}.")
            ->line("Assignment: {$this->title}")
            ->line("Due Date: {$this->dueDate}")
            ->action('View Homework', route('student.homework'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'homework_assigned',
            'title' => 'New Homework',
            'body' => "'{$this->title}' assigned in {$this->subject} ({$this->className}), due {$this->dueDate}.",
        ];
    }
}
