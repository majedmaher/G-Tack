<?php

namespace App\Services;

use App\Events\OrderCreated;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateOrderService
{
    public function handle($data)
    {
        DB::beginTransaction();
        try {
                $newOrder = Order::create([
                'customer_id' => Auth::user()->customer->id,
                'vendor_id' => $data['vendor_id'],
                'location_id' => $data['location_id'],
                'note' => $data['note'],
                'total' => $data['total'],
                'type' => $data['type'],
            ]);

            $addressOrder = Address::find($data['address_id']);

            $newOrder->address()->create([
                'order_id' => $newOrder->id,
                'address_id' => $addressOrder->id,
                'label' => $addressOrder->label,
                'user_name' => Auth::user()->customer->name,
                'user_phone' => Auth::user()->customer->phone,
                'lat' =>  $data['lat'] ?? $addressOrder->lat,
                'lng' =>  $data['lng'] ?? $addressOrder->lng,
                'map_address' =>  $data['map_address'] ?? $addressOrder->map_address,
                'description' =>  $data['description'] ?? $addressOrder->description,
            ]);

            foreach ($data['items'] as $value){
                $newOrder->items()->create([
                    'product_id' => $value['id'],
                    'product_name' => $value['product_name'],
                    'quantity' => $value['quantity'],
                    'price' => $value['price'],
                    'custom' => $value['custom'],
                ]);
            }
            DB::commit();
            
            event(new OrderCreated($newOrder));
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
