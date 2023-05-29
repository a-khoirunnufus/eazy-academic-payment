<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payment\ComponentDetail;
use App\Models\Payment\Payment;
use App\Models\LectureType;
use App\Models\Period;
use App\Models\Path;
use App\Models\Year;

class Student extends Model
{
    use HasFactory;
    
    protected $table = "hr.ms_student";

    protected $primaryKey = 'student_number';

    protected $fillable = [
        'path_id','period_id','msy_id','mlt_id','student_number','student_id','fullname','studyprogram_id'
    ];
    
    public function lectureType()
    {
        return $this->belongsTo(LectureType::class, 'mlt_id','mlt_id');
    }
    
    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id','period_id')->with('schoolyear');
    }
    
    public function path()
    {
        return $this->belongsTo(Path::class, 'path_id','path_id');
    }

    public function year()
    {
        return $this->belongsTo(Year::class, 'msy_id','msy_id');
    }

    public function componentDetail()
    {
        return $this->hasMany(ComponentDetail::class, 'studyprogram_id','mma_id')->orderBy('cd_id','asc')->with('component');
    }
    
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'student_number','student_number')->with('paymentDetail','paymentBill');
    }
    
    public function getComponent()
    {
        return $this->hasMany(ComponentDetail::class, 'mma_id','studyprogram_id')
        
        ->orderBy('cd_id','asc')->with('component');
    }
    
    public function studyProgram()
    {
        return $this->belongsTo(Studyprogram::class, 'studyprogram_id','studyprogram_id')->with('faculty');
    }
}
