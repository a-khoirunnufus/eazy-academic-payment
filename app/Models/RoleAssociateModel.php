<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleAssociateModel extends Model
{
    protected $table = "masterdata.role_associate_models";

    protected $guarded = [];

    protected $primaryKey = null;

    public $incrementing = false;
}
