<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SpoiledStockNotification extends Notification
{
    use Queueable;
    protected $spoiledStock;
    protected $transaction;
    protected $action;
    protected $message;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($spoiledStock, $transaction, $action, $message)
    {
        $this->spoiledStock = $spoiledStock;
        $this->transaction = $transaction;
        $this->action = $action;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
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
            'spoiledStock' => $this->spoiledStock,
            'transaction' => $this->transaction,
            'action' => $this->action,
            'message' => $this->message,
        ];
    }
}
