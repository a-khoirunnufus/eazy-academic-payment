<?php

namespace App\Models\Masterdata;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\HR\MsStudent as Student;

class MsUser extends Model
{
    protected $table = "masterdata.ms_users";

    protected $primaryKey = 'user_id';

    protected $fillable = [];

    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'email', 'user_email');
    }
}
