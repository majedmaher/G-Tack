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
        $order->customer->user->notify(new StatusOrderNotification($order));
    }
}
