<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Models\HasPermissions;

class Role extends Model
{
    use HasPermissions;

    protected $table = "masterdata.roles";

    protected $fillable = [
        'name', 'homepage_path'
    ];

    public function permissions()
    {
        return $this->hasMany(RoleHasPermissions::class);
    }

    public function associateModels()
    {
        return $this->hasMany(RoleAssociateModel::class);
    }
}
