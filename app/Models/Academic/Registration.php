<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Academic\StudentStudyCard;

class Registration extends Model
{
    use HasFactory;

    protected $table = "academic.registration";

    protected $primaryKey = 'id';

    protected $fillable = [
        'student_id','school_year_code','current_step_id','registration_type_id','studyprogram_id','studyprogram_name','approval_status','student_number','using_subject_package'
    ];

    public function studentStudyCard()
    {
        return $this->hasMany(StudentStudyCard::class, 'registration_id', 'id')->with('course');
    }
}

