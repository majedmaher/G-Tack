<?php

namespace App\Listeners;

use App\Events\ReOrdered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendReOrderNotification
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
     * @param  \App\Events\ReOrdered  $event
     * @return void
     */
    public function handle(ReOrdered $event)
    {
        $reorder = $event->reorder;
        // Send the notifciation
    }
}
