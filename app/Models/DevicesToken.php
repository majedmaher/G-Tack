<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevicesToken extends Model
{
    use HasFactory;

    protected $table = 'devices_tokens';

    protected $fillable = ['fcm_token' , 'user_id' , 'device_name'];
}
