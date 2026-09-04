<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FeePaymentReceived extends Notification
{
    use Queueable;

    public function __construct(
        public string $studentName,
        public string $invoiceTitle,
        public float $amount,
        public float $remainingBalance,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Payment Received: {$this->invoiceTitle}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A payment of Rs. " . number_format($this->amount, 2) . " has been recorded for {$this->studentName}.")
            ->line("Fee: {$this->invoiceTitle}")
            ->line("Remaining Balance: Rs. " . number_format($this->remainingBalance, 2));

        if ($this->remainingBalance <= 0) {
            $mail->line('This invoice is now fully paid. Thank you!');
        }

        return $mail->action('View Fees', route('student.fees'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'fee_payment',
            'title' => 'Payment Recorded',
            'body' => "Rs. " . number_format($this->amount, 2) . " paid for '{$this->invoiceTitle}'. Remaining: Rs. " . number_format($this->remainingBalance, 2) . ".",
        ];
    }
}
