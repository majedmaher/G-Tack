<?php

namespace App\Listeners;

use App\Events\UpdatedStatusOrder;
use App\Notifications\StatusOrderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
        if ($order->status == 'CANCELLED_BY_CUSTOMER') {
            $order->vendor->user->notify(new StatusOrderNotification($order));
        } elseif ($order->status == 'CANCELLED_BY_VENDOR' and $order->status == 'DECLINED') {
            $order->customer->user->notify(new StatusOrderNotification($order));
        } else {
            $order->customer->user->notify(new StatusOrderNotification($order));
        }
    }
}
