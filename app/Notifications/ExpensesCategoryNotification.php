<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExpensesCategoryNotification extends Notification
{
    use Queueable;
    protected $expensesCategory;
    protected $action;
    protected $message;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($expensesCategory, $action, $message)
    {
        $this->expensesCategory = $expensesCategory;
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
            'expensesCategory' => $this->expensesCategory,
            'action' => $this->action,
            'message' => $this->message,
        ];
    }
}
