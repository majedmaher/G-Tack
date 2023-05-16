<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use HasFactory , SoftDeletes;

    public function document()
    {
        return $this->belongsTo(Document::class , 'document_id' , 'id');
    }
}
