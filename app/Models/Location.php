<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory , SoftDeletes;

    public function regions()
    {
        return $this->hasMany(Location::class , 'parent_id' , 'id');
    }
}
