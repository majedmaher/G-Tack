<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory , SoftDeletes;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function(Order $order)  {
            // 20230001, 20230002
            $order->number = date('Y');
        });
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class , 'order_id' , 'id')
        ->select('id' , 'order_id' , 'jar_id' , 'quantity' , 'price')
        ->with(['jars:id,name,size,image']);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class , 'vendor_id' , 'id')
        ->select('id' , 'name' , 'commercial_name' , 'phone' , 'active');
    }
}
