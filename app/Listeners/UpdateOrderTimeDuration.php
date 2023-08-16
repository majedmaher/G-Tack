<?php

namespace App\Listeners;

use App\Events\UpdatedStatusOrder;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateOrderTimeDuration
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
     * @param  object  $event
     * @return void
     */
    public function handle(UpdatedStatusOrder $event)
    {
        if ($event->order->status == Order::STATUS_COMPLETED) {
            $time = now();
            $event->order->forceFill([
                'end_time' => $time,
                'time' => $time->diffInMinutes($event->order->start_time ?? $time),
            ]);
        }
    }
}
