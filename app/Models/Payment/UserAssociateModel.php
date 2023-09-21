<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAssociateModel extends Model
{
    protected $table = "masterdata.user_associate_models";

    protected $guarded = [];

    protected $primaryKey = null;

    public $incrementing = false;

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'user_id');
    }
}
