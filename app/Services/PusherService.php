<?php

namespace App\Services;

use App\Events\OrderTracking;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Pusher\Pusher;
use Throwable;

class PusherService
{
    public function handle($data)
    {
        try {
            $orders = Order::filter([
                'status' => 'ONWAY',
                // 'status2' => 'DELIVERING',
                'vendor_id' =>  Auth::user()->vendor->id,
            ])->get();
            foreach($orders as $order){
                event(new OrderTracking($order , $data , $data['channel_name'] , $data['socket_id']));
            }
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function auth($data){
        $pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            [
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'encrypted' => true,
            ]
        );
        $channel = $data['channel_name'];
        $socketId = $data['socket_id'];
        // Generate the authentication response with the channel name and socket ID
        $auth = $pusher->socket_auth($channel, $socketId);
        return response()->json($auth);
    }
}
