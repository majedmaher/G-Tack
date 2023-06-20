<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = ['type' , 'customer_id' , 'vendor_id' , 'status' , 'order_id' , 'content' , 'image'];

    public function getImageAttribute()
    {
        if (isset($this->attributes['image']) && is_string($this->attributes['image'])) {
            $imageUrl =  url('/') . '/' . $this->attributes['image'];
            return $imageUrl;
        }
        return null;
    }

}
