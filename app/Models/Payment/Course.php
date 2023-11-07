<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\Subject;

class Course extends Model
{
    use HasFactory;

    protected $table = "academic.course";

    protected $primaryKey = 'course_id';

    protected $fillable = [
        'subject_id', 'subject_code', 'subject_name', 'mandatory_status', 'credit'
    ];

    public function studyProgram()
    {
        return $this->belongsTo(Studyprogram::class, 'studyprogram_id','studyprogram_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id','subject_id');
    }
}
