<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory , SoftDeletes;

    protected $guarded = [];

    public function jars()
    {
        return $this->belongsTo(Jar::class , 'jar_id' , 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class , 'order_id' , 'id');
    }
}
