<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    use HasFactory , SoftDeletes;

    const STATUS_PENDING = 'PENDING';
    const STATUS_ACCEPTED = 'ACCEPTED';
    const STATUS_PROCESSING = 'PROCESSING';
    const STATUS_ONWAY = 'ONWAY';
    const STATUS_FILLED = 'FILLED';
    const STATUS_DELIVERED = 'DELIVERED';
    const STATUS_DECLINED = 'DECLINED';
    const STATUS_COMPLETED = 'COMPLETED';
    const STATUS_CANCELLED = 'CANCELLED';


    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function (Order $order) {
            $lastNumber = Order::whereDate('created_at', now())->max('number');
            if (!$lastNumber) {
                $order->number = now()->year . '0001';
            } else {
                $order->number = $lastNumber + 1;
            }
        });

        static::created(function (Order $order) {
            OrderStatus::create([
                'order_id' => $order->id,
                'customer_id' => Auth::user()->id,
                'vendor_id' => $order->vendor_id,
                'status' => 'PENDING',
                'note' => "1",
            ]);
        });
    }

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

    public function updateStatus($status)
    {
        if ($this->status == $status) {
            return;
        }
        
        $this->status = $status;
        $this->save();

        OrderStatus::create([
            'order_id' => $this->id,
            'customer_id' => Auth::user()->id,
            'vendor_id' => $this->vendor_id,
            'status' => $status,
            'note' => "1",
        ]);

    }
}
