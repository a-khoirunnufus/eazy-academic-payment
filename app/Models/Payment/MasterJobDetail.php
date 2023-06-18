<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterJobDetail extends Model
{
    use HasFactory;
    
    protected $table = "finance.ms_jobs_detail";

    protected $primaryKey = 'mjd_id';

    protected $fillable = ['mj_id','title', 'status'];
    
}
