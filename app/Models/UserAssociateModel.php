<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAssociateModel extends Model
{
    protected $table = "user_associate_models";

    protected $guarded = [];

    protected $primaryKey = null;

    public $incrementing = false;

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'user_id');
    }
}
