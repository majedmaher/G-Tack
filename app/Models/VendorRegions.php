<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorRegions extends Model
{
    use HasFactory;

    public function region()
    {
        return $this->belongsTo(Location::class, 'region_id' , 'id');
    }
}
