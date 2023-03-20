<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionAssociateModel extends Model
{
    protected $guarded = [];

    protected $primaryKey = null;

    public $incrementing = false;

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
