<?php

namespace App\Models\Student;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Year;
use App\Models\Student;

class CreditSubmission extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table = "finance.ms_credit_submission";

    protected $primaryKey = 'mcs_id';

    protected $fillable = ['student_number', 'msy_id', 'mcs_phone','mcs_email','mcs_reason','mcs_proof','mcs_status'];
    
    public function period()
    {
        return $this->belongsTo(Year::class, 'msy_id','msy_id');
    }
    
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_number','student_number')->with('studyProgram');
    }
}
