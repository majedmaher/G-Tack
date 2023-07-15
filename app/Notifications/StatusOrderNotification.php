<?php

namespace App\Notifications;

use App\Http\Controllers\ControllersService;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidFcmOptions;
use NotificationChannels\Fcm\Resources\AndroidNotification;
use NotificationChannels\Fcm\Resources\ApnsConfig;
use NotificationChannels\Fcm\Resources\ApnsFcmOptions;

class StatusOrderNotification extends Notification
{
    use Queueable;

    protected Order $order;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Order $order)
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
        return [
            'database',
            FcmChannel::class,
        ];
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
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }


    public function toFcm($notifiable)
    {
        $title = $this->order->vendor->commercial_name;
        $body = ControllersService::getMessage($this->order->status) .' : ' . $this->order->number;
        if($this->order->status == 'CANCELLED_BY_CUSTOMER'){
            $body = ControllersService::getMessage($this->order->status) . ' : ' . $this->order->customer->name;
        }
        if($this->order->status == 'CANCELLED_BY_VENDOR' and $this->order->status == 'DECLINED'){
            $body = ControllersService::getMessage($this->order->status) . ' : ' . $this->order->number;
        }
        return FcmMessage::create()
            ->setNotification(\NotificationChannels\Fcm\Resources\Notification::create()
                ->setTitle($title)
                ->setBody($body));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $title = $this->order->vendor->commercial_name;
        $body = ControllersService::getMessage($this->order->status) .' : ' . $this->order->number;
        if($this->order->status == 'CANCELLED_BY_CUSTOMER'){
            $body = ControllersService::getMessage($this->order->status) . ' : ' . $this->order->customer->name;
        }
        if($this->order->status == 'CANCELLED_BY_VENDOR' and $this->order->status == 'DECLINED'){
            $body = ControllersService::getMessage($this->order->status) . ' : ' . $this->order->number;
        }
        return [
            'title' => $title,
            'body' => $body,
        ];
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
