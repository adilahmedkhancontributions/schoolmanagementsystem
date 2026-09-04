<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FeeInvoiceGenerated extends Notification
{
    use Queueable;

    public function __construct(
        public string $studentName,
        public string $invoiceTitle,
        public float $amount,
        public string $dueDate,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Fee Invoice: {$this->invoiceTitle}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new fee invoice has been generated for {$this->studentName}.")
            ->line("Fee: {$this->invoiceTitle}")
            ->line("Amount: Rs. " . number_format($this->amount, 2))
            ->line("Due Date: {$this->dueDate}")
            ->line('Please ensure payment is made before the due date.')
            ->action('View Fees', route('student.fees'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'fee_invoice',
            'title' => 'New Fee Invoice',
            'body' => "Fee invoice '{$this->invoiceTitle}' for {$this->studentName} — Rs. " . number_format($this->amount, 2) . " due by {$this->dueDate}.",
        ];
    }
}
