<?php

namespace App\Services;

use App\Events\OrderTracking;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
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
                event(new OrderTracking($order , $data));
            }
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
