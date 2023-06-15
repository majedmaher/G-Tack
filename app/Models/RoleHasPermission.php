<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleHasPermission extends Model
{
    use HasFactory;

    protected $fillable = ['role_id' , 'permission_id'];

    public function permission()
    {
        return $this->hasOne(Permission::class, 'permission_id');
    }
}
