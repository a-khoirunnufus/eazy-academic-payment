<?php

namespace App\Models\PMB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\LectureType;
use App\Models\Payment\Path;
use App\Models\Payment\Period;
use App\Models\Payment\Year;

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
        return $this->hasOne(Studyprogram::class, 'studyprogram_id', 'reg_major_pass')->with('faculty');
    }

    public function lectureType()
    {
        return $this->hasOne(LectureType::class, 'mlt_id', 'reg_major_lecture_type_pass');
    }

    public function payment()
    {
        return $this->hasOne(PaymentRegister::class, 'reg_id', 'reg_id')->with('PaymentRegisterDetail');
    }

    public function period()
    {
        return $this->belongsTo(Period::class, 'ms_period_id', 'period_id');
    }

    public function path()
    {
        return $this->belongsTo(Path::class, 'ms_path_id', 'path_id');
    }

    public function year()
    {
        return $this->belongsTo(Year::class, 'ms_school_year_id', 'msy_id');
    }
}
