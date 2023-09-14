<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LectureType extends Model
{
    use HasFactory;

    protected $table = "masterdata.ms_lecture_type";

    protected $primaryKey = 'mlt_id';

    protected $fillable = [
        'mlt_name'
    ];

}
