<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = ['document_id' , 'vendor_id' , 'file_path' , 'file_name' , 'status'];

    public function getFilePathAttribute()
    {
        return Request::root('/') . '/' . $this->attributes['file_path'];
    }

    public function document()
    {
        return $this->belongsTo(Document::class , 'document_id' , 'id');
    }
}
