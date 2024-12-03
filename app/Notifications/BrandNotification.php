<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BrandNotification extends Notification
{
    use Queueable;
    protected $brand;
    protected $action;
    protected $message; 
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($brand, $action, $message)
    {
        $this->brand = $brand;
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
            'brand' => $this->brand,
            'action' => $this->action,
            'message' => $this->message,
        ];
    }
}
