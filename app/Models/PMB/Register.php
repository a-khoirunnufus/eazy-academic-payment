<?php

namespace App\Models\PMB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Register extends Model
{
    protected $table = 'pmb.register';
    protected $primaryKey = 'reg_id';
    protected $fillable = [];

    public function participant(): hasOne
    {
        return $this->hasOne(Participant::class, 'par_id', 'par_id');
    }
}
