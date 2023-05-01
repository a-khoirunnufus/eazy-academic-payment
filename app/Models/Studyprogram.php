<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Studyprogram extends Model
{
    use HasFactory;
    
    protected $table = "masterdata.ms_studyprogram";

    protected $primaryKey = 'studyprogram_id';

    protected $fillable = [
        'studyprogram_name', 'studyprogram_name_english'
    ];
}
