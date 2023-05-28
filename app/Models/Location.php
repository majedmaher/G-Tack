<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Location extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = ['name' , 'parent_id' , 'type'];

    public function regions()
    {
        return $this->hasMany(Location::class , 'parent_id' , 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class , 'governorate_id' , 'id');
    }

    public function orders2()
    {
        return $this->hasMany(Order::class , 'region_id' , 'id');
    }

    public function vendor()
    {
        return $this->hasMany(Vendor::class , 'governorate_id' , 'id');
    }
}
