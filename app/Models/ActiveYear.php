<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActiveYear extends Model
{
    use HasFactory;
    
    protected $table = "academic.school_year";

    protected $primaryKey = 'school_year_id';

    protected $fillable = [
        'school_year','semester','start_date','end_date'
    ];
}
