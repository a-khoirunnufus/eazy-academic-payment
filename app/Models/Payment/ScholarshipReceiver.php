<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Payment\Year;
use App\Models\Payment\Student;
use App\Models\Payment\Scholarship;
use App\Models\PMB\Register;

class ScholarshipReceiver extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "finance.ms_scholarship_receiver";

    protected $primaryKey = 'msr_id';

    protected $fillable = ['ms_id', 'student_number', 'msr_period','msr_nominal','msr_status','msr_status_generate','prr_id','reg_id'];

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class, 'ms_id','ms_id')->withTrashed();
    }

    public function period()
    {
        return $this->belongsTo(Year::class, 'msr_period','msy_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_number','student_number')->with('studyProgram');
    }

    public function newStudent()
    {
        return $this->belongsTo(Register::class, 'reg_id','reg_id')->with('studyprogram','participant');
    }
}
