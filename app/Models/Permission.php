<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $guarded = [];

    public function associateModels()
    {
        return $this->hasMany(PermissionAssociateModel::class);
    }

    public function getTransNameAttribute()
    {
        if($this->default_module_permission)
            return "Akses " . trans("modules.modules." . str_replace("access_", "", $this->name));

        return trans("permissions." . $this->name);
    }
}
