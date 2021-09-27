<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class packingwavesCompletedNotification extends Notification
{
    use Queueable;
    private $packingwaveData;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($packingwaveData)
    {
        $this->packingwaveData = $packingwaveData;
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
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)                    
                    ->greeting($this->packingwaveData['name'])
                    ->line($this->packingwaveData['body'])
                    ->action($this->packingwaveData['text'], $this->packingwaveData['actionUrl'])
                    ->line($this->packingwaveData['thanks']);
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
            'name' => $this->packingwaveData['name'],
            'text' => $this->packingwaveData['text'],
            'id' => $this->packingwaveData['id'],
            'sender_id' => $this->packingwaveData['sender_id'],
            'sender_name' => $this->packingwaveData['sender_name'],
            'receiver_name' => $this->packingwaveData['receiver_name'],
            'actionUrl'=> $this->packingwaveData['actionUrl']
        ];
    }
}
