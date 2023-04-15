<?php
namespace App\Services;

use App\Events\ReOrdered;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class ReOrderService
{
    public function handle($id){
        DB::beginTransaction();
        try {
            $oldOrder = Order::with('address' , 'items')->find($id);
            $newOrder = $oldOrder->replicate();
            $newOrder->save();
            foreach ($oldOrder->items as $item) {
                $newItem = $item->replicate();
                $newOrder->items()->save($newItem);
            }
            foreach ($oldOrder->statuses as $status) {
                if($status->status != 'PENDING'){
                    $newStatus = $status->replicate();
                    $newOrder->statuses()->save($newStatus);
                }
            }
            $newAddress = $oldOrder->address->replicate();
            $newAddress->order_id = $newOrder->id;
            $newOrder->address()->save($newAddress);
            $newOrder = $oldOrder->address->replicate();
            DB::commit();
            event(new ReOrdered());
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

}
