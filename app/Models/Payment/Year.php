<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    use HasFactory;

    protected $table = "masterdata.ms_school_year";

    protected $primaryKey = 'msy_id';

    protected $fillable = [
        'msy_year','msy_semester','msy_code','msy_status'
    ];
}
