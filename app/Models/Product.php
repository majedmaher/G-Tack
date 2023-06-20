<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;

class Product extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = ['type' , 'name' , 'price' , 'status' , 'size' , 'image'];

    public function getImageAttribute()
    {
        return Request::root('/') . '/' . $this->attributes['image'];
    }
}
