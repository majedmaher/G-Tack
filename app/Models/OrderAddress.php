<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    use HasFactory;

    protected $fillable = ['order_id' , 'address_id' , 'lat' , 'lng' , 'label' , 'map_address' , 'description' , 'user_name' , 'user_phone'];

    public function order()
    {
        return $this->belongsTo(Order::class , 'order_id' , 'id');
    }
}
