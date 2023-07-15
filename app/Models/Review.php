<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class , 'vendor_id' , 'id')
        ->select('id' , 'type' , 'avatar' , 'name' , 'commercial_name' , 'phone' , 'active');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class , 'customer_id' , 'id')
        ->select('id' , 'name' , 'phone');
    }

    public function order()
    {
        return $this->belongsTo(Order::class , 'order_id' , 'id');
    }
}
