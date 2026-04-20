<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InAppActivityNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private array $payload)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->payload['title'] ?? 'Notification',
            'message' => $this->payload['message'] ?? '',
            'url' => $this->payload['url'] ?? null,
            'type' => $this->payload['type'] ?? 'info',
            'icon' => $this->payload['icon'] ?? 'fa-solid fa-bell',
            'category' => $this->payload['category'] ?? 'general',
            'actor_name' => $this->payload['actor_name'] ?? null,
            'meta' => $this->payload['meta'] ?? [],
            'created_at_human' => now()->format('M d, Y h:i A'),
        ];
    }
}
