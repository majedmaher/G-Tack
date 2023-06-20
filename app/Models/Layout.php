<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class Layout extends Model
{
    use HasFactory;

    protected  $fillable = ['type' , 'image' , 'title' , 'description'];

    public function getImageAttribute()
    {
        return Request::root('/') . '/' . $this->attributes['image'];
    }
}
