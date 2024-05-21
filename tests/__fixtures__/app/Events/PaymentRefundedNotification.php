<?php

namespace DuncanMcClean\SimpleCommerce\Tests\Fixtures\Events;

use DuncanMcClean\SimpleCommerce\Contracts\Orders\Order as OrderContract;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRefundedNotification extends Notification
{
    public function __construct(protected OrderContract $order)
    {
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }
}
