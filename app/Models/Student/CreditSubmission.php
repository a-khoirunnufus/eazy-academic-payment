<?php

namespace App\Models\Student;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Year;
use App\Models\Student;
use App\Models\Payment\payment;

class CreditSubmission extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table = "finance.ms_credit_submission";

    protected $primaryKey = 'mcs_id';

    protected $fillable = ['student_number', 'mcs_school_year', 'mcs_phone','mcs_email','mcs_reason','mcs_proof','mcs_status','mcs_proof_filename','mcs_method','mcs_decline_reason'];
    
    public function period()
    {
        return $this->belongsTo(Year::class, 'mcs_school_year','msy_code');
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
