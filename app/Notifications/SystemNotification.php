<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $subject;
    public $title;
    public $message;
    public $actionUrl;
    public $actionText;
    public $details;
    public $reference;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        string $subject,
        string $title,
        string $message,
        ?string $actionUrl = null,
        ?string $actionText = null,
        ?array $details = null,
        ?string $reference = null
    ) {
        $this->subject = $subject;
        $this->title = $title;
        $this->message = $message;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
        $this->details = $details;
        $this->reference = $reference;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->view('emails.notification', [
                'user' => $notifiable,
                'subject' => $this->subject,
                'title' => $this->title,
                'message' => $this->message,
                'actionUrl' => $this->actionUrl,
                'actionText' => $this->actionText,
                'details' => $this->details,
                'reference' => $this->reference,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subject' => $this->subject,
            'title' => $this->title,
            'message' => $this->message,
            'actionUrl' => $this->actionUrl,
            'actionText' => $this->actionText,
            'details' => $this->details,
            'reference' => $this->reference,
        ];
    }
}
