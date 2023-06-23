<?php

namespace App\Models\PMB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Model
{
    protected $table = "pmb.users";

    protected $primaryKey = 'user_id';

    protected $fillable = [];

    public function participant(): HasOne
    {
        return $this->hasOne(Participant::class, 'user_id', 'user_id');
    }
}
