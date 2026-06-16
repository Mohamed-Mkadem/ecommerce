<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

use App\Services\NotificationUrlGenerator;

class OrderPlacedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'New Order!',
            'message' => 'A new order has been placed!',
            'amount' => number_format($this->order->amount / 1000, 3, '.', ''),
            'icon' => 'ri-shopping-cart-fill',
            'url' => route('orders.show',  $this->order),
            'created_at' => now()->format('d - m - Y : H:i')
        ];
    }
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'New Order!',
            'message' => 'A new order has been placed!',
            'amount' => number_format($this->order->amount / 1000, 3, '.', ''),
            'icon' => 'ri-shopping-cart-fill',
            'url' => route('orders.show',  $this->order),
            'created_at' => now()->format('d - m - Y : H:i')
        ]);
    }

    public function databaseType(object $notifiable): string
    {
        return 'new-order';
    }
}
