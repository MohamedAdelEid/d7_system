<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PurchaseNotification extends Notification
{
    use Queueable;
    protected $transaction;
    protected $transactionPurchaseLines;
    protected $action;
    protected $message;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($transaction, $transactionPurchaseLines, $action, $message)
    {
        $this->transaction = $transaction;
        $this->transactionPurchaseLines = $transactionPurchaseLines;
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
            'transaction' => $this->transaction,
            'transactionPurchaseLines' => $this->transactionPurchaseLines,
            'action' => $this->action,
            'message' => $this->message,
        ];
    }
}
