<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\LectureType;
use App\Models\Payment\Year;
use App\Models\Payment\Period;
use App\Models\Payment\Path;
use App\Models\Payment\ComponentDetail;
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

    public function getComponent()
    {
        return $this->hasMany(ComponentDetail::class, 'mma_id','studyprogram_id')

        ->orderBy('cd_id','asc')->with('component');
    }
}
