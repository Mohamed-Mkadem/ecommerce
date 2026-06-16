<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendingOrderToShippingCompanyNotification extends Notification
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
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Warning !',
            'order_id' => $this->order->id,
            'message' => "Order could not be sent to the shipping company.",
            'icon' => 'ri-error-warning-line',
            'url' => route('orders.show',  $this->order),
            'created_at' => now()->format('d - m - Y : H:i')
        ];
    }

    public function databaseType(object $notifiable): string
    {
        return 'new-failed-order';
    }
}
