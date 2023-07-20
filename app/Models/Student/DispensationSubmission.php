<?php

namespace App\Models\Student;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Year;
use App\Models\Student;
use App\Models\Payment\payment;

class DispensationSubmission extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table = "finance.ms_dispensation_submission";

    protected $primaryKey = 'mds_id';

    protected $fillable = ['student_number', 'mds_school_year', 'mds_phone','mds_email','mds_reason','mds_proof','mds_deadline','mds_status','mds_proof_filename','mds_decline_reason'];
    
    public function period()
    {
        return $this->belongsTo(Year::class, 'mds_school_year','msy_code');
    }
    
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_number','student_number')->with('studyProgram','payment');
    }
    
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'student_number','student_number')->with('paymentDetail','paymentBill');
    }
}
