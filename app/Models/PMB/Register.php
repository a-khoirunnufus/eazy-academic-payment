<?php

namespace App\Models\PMB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Studyprogram;
use App\Models\LectureType;

class Register extends Model
{
    protected $table = 'pmb.register';
    protected $primaryKey = 'reg_id';
    protected $fillable = [];

    public function participant(): hasOne
    {
        return $this->hasOne(Participant::class, 'par_id', 'par_id');
    }

    public function studyprogram()
    {
        return $this->hasOne(Studyprogram::class, 'studyprogram_id', 'reg_major_pass');
    }

    public function lectureType()
    {
        return $this->hasOne(LectureType::class, 'mlt_id', 'reg_major_lecture_type_pass');
    }
}
