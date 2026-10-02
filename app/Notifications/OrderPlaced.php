<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlaced extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject(__('Order :number confirmed — :store', ['number' => $this->order->order_number, 'store' => setting('site_name', 'NOKSHI')]))
            ->greeting(__('Thank you, :name!', ['name' => $this->order->customer_name]))
            ->line(__('We have received your order and are getting it ready.'));

        foreach ($this->order->items as $item) {
            $mail->line("• {$item->name}".($item->variant_label ? " ({$item->variant_label})" : '')." × {$item->quantity} — ".money($item->total));
        }

        return $mail
            ->line(__('Total: :total', ['total' => money($this->order->total)]))
            ->line(__('Payment: :method', ['method' => $this->order->payment_label]))
            ->action(__('Track your order'), route('order.track', $this->order->order_number))
            ->line(__('Questions? Just reply to this email.'));
    }
}
