<?php

namespace App\Models\PMB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\LectureType;
use App\Models\Payment\Path;
use App\Models\Payment\Period;
use App\Models\Payment\Year;
use App\Models\Payment\Payment;
use App\Models\Payment\ComponentDetail;

class Register extends Model
{
    protected $table = 'pmb.register';
    protected $primaryKey = 'reg_id';
    protected $fillable = [];

    public function participant(): hasOne
    {
        return $this->hasOne(Participant::class, 'par_id', 'par_id');
    }

    public function studyProgram()
    {
        return $this->hasOne(Studyprogram::class, 'studyprogram_id', 'reg_major_pass')->with('faculty');
    }

    public function getComponent()
    {
        return $this->hasMany(ComponentDetail::class, 'mma_id','reg_major_pass')

        ->orderBy('cd_id','asc')->with('component');
    }

    public function lectureType()
    {
        return $this->hasOne(LectureType::class, 'mlt_id', 'reg_major_lecture_type_pass');
    }

    public function paymentRegister()
    {
        return $this->hasOne(PaymentRegister::class, 'reg_id', 'reg_id')->with('PaymentRegisterDetail');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'reg_id','reg_id')->with('paymentDetail','paymentBill');
    }

    public function period()
    {
        return $this->belongsTo(Period::class, 'ms_period_id', 'period_id')->with('schoolyear');
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
