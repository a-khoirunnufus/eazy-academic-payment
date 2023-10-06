<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Payment\Year;
use App\Models\Payment\Student;
use App\Models\Payment\Payment;

class CreditSubmission extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "finance.ms_credit_submission";

    protected $primaryKey = 'mcs_id';

    protected $fillable = ['student_number', 'mcs_school_year', 'mcs_phone','mcs_email','mcs_reason','mcs_proof','mcs_status','mcs_proof_filename','mcs_decline_reason','prr_id','cs_id'];

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
