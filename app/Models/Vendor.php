<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Request;

class Vendor extends Model
{
    use HasFactory , Notifiable, SoftDeletes;

    protected $fillable = ['name' , 'type' , 'commercial_name' , 'avatar' , 'phone' , 'user_id' , 'governorate_id' , 'region_id' , 'max_orders' , 'max_product' , 'active' , 'lat' , 'lng'];

    public function user()
    {
        return $this->belongsTo(User::class , 'user_id' , 'id');
    }

    public function governorate()
    {
        return $this->belongsTo(Location::class , 'governorate_id' , 'id');
    }

    public function region()
    {
        return $this->belongsTo(Location::class , 'region_id' , 'id');
    }

    public function regions()
    {
        return $this->hasMany(VendorRegions::class , 'vendor_id' , 'id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class , 'vendor_id' , 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class , 'vendor_id' , 'id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class , 'vendor_id' , 'id');
    }

    public function getAvatarAttribute()
    {
        return Request::root('/') . '/' . $this->attributes['image'];
    }

    public function delete()
    {
        $this->user->delete();
        parent::delete();
    }
}
