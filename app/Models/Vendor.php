<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory , SoftDeletes;


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Location::class , 'governorate_id' , 'id');
    }

    public function region()
    {
        return $this->belongsTo(Location::class , 'region_id' , 'id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class , 'vendor_id' , 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class , 'vendor_id' , 'id');
    }
}
