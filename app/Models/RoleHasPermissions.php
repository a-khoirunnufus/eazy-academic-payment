<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleHasPermissions extends Model
{
    protected $table = "masterdata.role_has_permissions";

    protected $guarded = [];

    protected $primaryKey = null;

    public $incrementing = false;

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
