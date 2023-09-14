<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    protected $table = "masterdata.ms_faculties";

    protected $primaryKey = 'faculty_id';

    protected $fillable = [
        'faculty_name'
    ];

    public function studyProgram()
    {
        return $this->hasMany(Studyprogram::class, 'faculty_id','faculty_id');
    }
}
