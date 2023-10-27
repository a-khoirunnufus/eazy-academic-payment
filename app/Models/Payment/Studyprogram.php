<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Studyprogram extends Model
{
    use HasFactory;

    protected $table = "masterdata.ms_studyprogram";

    protected $primaryKey = 'studyprogram_id';

    protected $fillable = [
        'faculty_id','studyprogram_name', 'studyprogram_name_english'
    ];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id','faculty_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'studyprogram_id', 'studyprogram_id')->with('payment');
    }
}
