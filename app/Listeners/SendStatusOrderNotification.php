<?php

namespace App\Listeners;

use App\Events\UpdatedStatusOrder;
use App\Http\Controllers\ControllersService;
use App\Models\User;
use App\Notifications\AdminNotification;
use App\Notifications\StatusOrderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendStatusOrderNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\UpdatedStatusOrder  $event
     * @return void
     */
    public function handle(UpdatedStatusOrder $event)
    {
        $order = $event->order;
        $title = $order->vendor->commercial_name;
        $body = ControllersService::getMessage($order->status) .' : ' . $order->numder;
        if ($order->status == 'CANCELLED_BY_CUSTOMER') {
            $order->vendor->user->notify(new StatusOrderNotification($order));
            $body = ControllersService::getMessage($order->status) . ' : ' . $order->customer->name;
        } elseif ($order->status == 'CANCELLED_BY_VENDOR' and $order->status == 'DECLINED') {
            $order->customer->user->notify(new StatusOrderNotification($order));
            $body = ControllersService::getMessage($order->status) . ' : ' . $order->numder;
        } else {
            $order->customer->user->notify(new StatusOrderNotification($order));
        }

        // Send the notifciation for admin
        $users = User::whereIn('type' , ['ADMIN' , 'USER'])->get();
        Notification::send($users, new AdminNotification($title , $body));
    }
}
