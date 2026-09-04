<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnnouncementPublished extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $body,
        public string $announcedBy,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Announcement: {$this->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new announcement has been published.")
            ->line("{$this->title}")
            ->line($this->body)
            ->line("Announced by: {$this->announcedBy}");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'announcement',
            'title' => $this->title,
            'body' => $this->body,
            'announced_by' => $this->announcedBy,
        ];
    }
}
