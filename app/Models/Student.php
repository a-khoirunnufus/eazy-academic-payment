<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    
    protected $table = "hr.ms_student";

    protected $primaryKey = 'student_id';

    protected $fillable = [
        'path_id','period_id','msy_id','mlt_id','student_number','student_id','fullname','studyprogram_id'
    ];
    
}
