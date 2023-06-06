<?php

namespace App\Models\PMB;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $table = "pmb.participant";

    protected $primaryKey = 'par_id';

    protected $fillable = [];
}
