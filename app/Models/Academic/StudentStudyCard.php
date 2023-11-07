<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payment\Course;

class StudentStudyCard extends Model
{
    use HasFactory;

    protected $table = "academic.student_study_card";

    protected $fillable = [
        'registration_id','course_id','student_id','semester','grade'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id','course_id')->with('subject');
    }
}
