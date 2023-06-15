<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Course;
use App\Models\Studyprogram;

class CourseRate extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table = "finance.ms_course_rates";

    protected $primaryKey = 'mcr_id';

    protected $fillable = [
        'mcr_course_id','mcr_tingkat','mcr_rate','mcr_active_status','mcr_is_package','mcr_studyprogram_id'
    ];
    
    public function course()
    {
        return $this->belongsTo(Course::class, 'mcr_course_id','course_id');
    }
    
    public function studyProgram()
    {
        return $this->belongsTo(Studyprogram::class, 'mcr_studyprogram_id','studyprogram_id');
    }
}
