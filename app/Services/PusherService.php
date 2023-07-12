<?php

namespace App\Services;

use App\Events\OrderTracking;
use App\Models\Order;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
use Pusher\Pusher;
use Throwable;

class PusherService
{
    public function handle($data)
    {
        try {
            $orders = Order::whereIn('status' , ['DELIVERING' , 'ONWAY'])->where('vendor_id' , Auth::user()->vendor->id)->get();
            Vendor::find(Auth::user()->vendor->id)->update([
                "lat" => isset($data["lat"]),
                "lng" => isset($data["lng"]),
            ]);
            foreach($orders as $order){
                event(new OrderTracking($order , $data));
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
