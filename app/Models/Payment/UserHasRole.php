<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHasRole extends Model
{
    protected $table = "masterdata.user_has_roles";

    protected $guarded = [];

    protected $primaryKey = null;

    public $incrementing = false;

    public function role()
    {
        return $this->belongsTo(\App\Models\Role::class, 'role_id', 'id');
    }
}
