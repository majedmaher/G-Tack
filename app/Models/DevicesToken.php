<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DevicesToken extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = ['fcm_token' , 'user_id' , 'device_name'];
}
