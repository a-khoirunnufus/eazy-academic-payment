<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Studyprogram;
use App\Models\LectureType;

class MsStudent extends Model
{
    protected $table = "hr.ms_student";

    protected $primaryKey = 'student_id';

    protected $fillable = [];

    public function studyprogram(): HasOne
    {
        return $this->hasOne(Studyprogram::class, 'studyprogram_id', 'studyprogram_id');
    }

    public function lectureType()
    {
        return $this->hasOne(LectureType::class, 'mlt_id', 'mlt_id');
    }
}
