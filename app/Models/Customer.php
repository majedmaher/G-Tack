<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Customer extends Model
{
    use HasFactory , Notifiable , SoftDeletes;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class , 'customer_id' , 'id');
    }

    public function governorate()
    {
        return $this->belongsTo(Location::class , 'governorate_id' , 'id');
    }

    public function region()
    {
        return $this->belongsTo(Location::class , 'region_id' , 'id');
    }
}
