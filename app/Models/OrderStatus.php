<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;

    protected $table = "order_status";

    protected $fillable = ['order_id' , 'customer_id' , 'vendor_id' , 'reason_id' , 'status' , 'note'];

    const STATUS_PENDING = 'PENDING';
    const STATUS_ACCEPTED = 'ACCEPTED';
    const STATUS_DECLINED = 'DECLINED';
    const STATUS_ONWAY = 'ONWAY';
    const STATUS_PROCESSING = 'PROCESSING';
    const STATUS_FILLED = 'FILLED';
    const STATUS_DELIVERED = 'DELIVERED';
    const STATUS_COMPLETED = 'COMPLETED';
    const STATUS_CANCELLED_BY_VENDOR = 'CANCELLED_BY_VENDOR';
    const STATUS_CANCELLED_BY_CUSTOMER = 'CANCELLED_BY_CUSTOMER';

    public function reason()
    {
        return $this->belongsTo(Reason::class , 'reason_id' , 'id');
    }
}
