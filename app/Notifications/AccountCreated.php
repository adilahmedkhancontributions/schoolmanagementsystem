<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountCreated extends Notification
{
    use Queueable;

    public function __construct(
        public string $role,
        public string $email,
        public string $password,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your School Account Has Been Created')
            ->greeting("Hello {$notifiable->name},")
            ->line("A new {$this->role} account has been created for you.")
            ->line("Email: {$this->email}")
            ->line("Password: {$this->password}")
            ->line('Please log in and change your password as soon as possible.')
            ->line('If you did not expect this email, please contact your school administrator.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'account_created',
            'title' => 'Account Created',
            'body' => "Your {$this->role} account has been created. Please log in and change your password.",
        ];
    }
}
