<?php

namespace App\Models\PMB;

use Illuminate\Database\Eloquent\Model;
use App\Models\PMB\User;

class Participant extends Model
{
    protected $table = "pmb.participant";

    protected $primaryKey = 'par_id';

    protected $fillable = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function register()
    {
        return $this->belongsTo(Register::class, 'par_id', 'par_id');
    }
}
