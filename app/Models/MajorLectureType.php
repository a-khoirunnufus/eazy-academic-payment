<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MajorLectureType extends Model
{
    use HasFactory;
    
    protected $table = "masterdata.ms_major_lecture_type";

    protected $primaryKey = 'mma_lt_id';

    protected $fillable = [
        'mma_id','mlt_id'
    ];
    
    public function studyProgram()
    {
        return $this->belongsTo(Studyprogram::class, 'mma_id','studyprogram_id');
    }
    
    public function lectureType()
    {
        return $this->belongsTo(lectureType::class, 'mlt_id','mlt_id');
    }
    
    
}
