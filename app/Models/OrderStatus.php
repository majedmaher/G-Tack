<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderStatus extends Model
{
    use HasFactory , SoftDeletes;

    protected $table = "order_status";

    protected $guarded = [];

    public function reason()
    {
        return $this->belongsTo(Reason::class , 'reason_id' , 'id');
    }
}
