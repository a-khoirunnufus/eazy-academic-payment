<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Studyprogram;
use App\Models\LectureType;
use App\Models\Year;
use App\Models\Period;
use App\Models\Path;
use App\Models\Masterdata\MsUser;

class MsStudent extends Model
{
    protected $table = "hr.ms_student";

    protected $primaryKey = 'student_id';

    protected $fillable = [];

    public function user()
    {
        return $this->belongsTo(MsUser::class, 'email', 'user_email');
    }

    public function studyprogram(): HasOne
    {
        return $this->hasOne(Studyprogram::class, 'studyprogram_id', 'studyprogram_id');
    }

    public function lectureType()
    {
        return $this->hasOne(LectureType::class, 'mlt_id', 'mlt_id');
    }

    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id','period_id');
    }

    public function path()
    {
        return $this->belongsTo(Path::class, 'path_id','path_id');
    }

    public function year()
    {
        return $this->belongsTo(Year::class, 'msy_id','msy_id');
    }
}
