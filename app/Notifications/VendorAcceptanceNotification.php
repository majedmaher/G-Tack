<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;

class VendorAcceptanceNotification extends Notification
{
    use Queueable;
    public $title = "تطبيق جيتك";
    public $body = "تم قبول الحساب يرجى مراجعة التطبيق";
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [
            'database',
            FcmChannel::class,
        ];
    }

    public function toFcm($notifiable)
    {
        return FcmMessage::create()
        ->setNotification(\NotificationChannels\Fcm\Resources\Notification::create()
            ->setTitle($this->title)
            ->setBody($this->body));

    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
        ];
    }
}
