<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = ['document_id' , 'vendor_id' , 'file_path' , 'status'];

    public function document()
    {
        return $this->belongsTo(Document::class , 'document_id' , 'id');
    }
}
