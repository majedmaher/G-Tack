<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory , SoftDeletes;

    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(OrderItem::class , 'order_id' , 'id')
        ->select('id' , 'order_id' , 'jar_id' , 'quantity' , 'price')
        ->with(['jars:id,name,size,price,image']);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class , 'vendor_id' , 'id')
        ->select('id' , 'name' , 'commercial_name' , 'phone' , 'active');
    }

    public function address()
    {
        return $this->hasOne(OrderAddress::class , 'order_id' , 'id');
    }
}
