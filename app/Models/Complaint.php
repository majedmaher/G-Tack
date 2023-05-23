<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = ['type' , 'customer_id' , 'vendor_id' , 'order_id' , 'content' , 'image'];
}
