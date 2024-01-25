<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('O nouă comandă a fost plasată!')
                    ->line('Detalii comandă:')
                    ->line('Id comandă: ' . $this->order->id)
                    ->line('Nume client: ' . $this->order->first_name . " " . $this->order->last_name)
                    ->line('Adresa livrare: ' . $this->order->country . ", " . $this->order->city . ", " . $this->order->address . ", " . $this->order->number . "," . $this->order->phone)
                    ->line('Pret total: ' . $this->order->total_price)
                    ->action('Vizualizează comanda', url('/orders/' . $this->order->id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
