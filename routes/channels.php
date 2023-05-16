<?php

use App\Models\Order;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('order-tracking-{id}' , function ($user, $id) {
    return Order::where('id', '=', $id)
        ->whereExists(function ($query) use ($user) {
            $query->select(DB::raw('1'))
                ->from('customers')
                ->whereColumn('customers.id', '=', 'orders.customer_id')
                ->where('customers.user_id', '=', $user->id);
        })
        ->exists();
});
