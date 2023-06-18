<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterJob extends Model
{
    use HasFactory;
    
    protected $table = "finance.ms_jobs";

    protected $primaryKey = 'mj_id';

    protected $fillable = ['queue', 'user_id', 'status'];
}
