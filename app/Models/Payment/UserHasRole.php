<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHasRole extends Model
{
    protected $table = "user_has_roles";

    protected $guarded = [];

    protected $primaryKey = null;

    public $incrementing = false;

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
