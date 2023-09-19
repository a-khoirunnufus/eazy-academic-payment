<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogActivityDetail extends Model
{
    use HasFactory;

    protected $table = "finance.log_activity_detail";

    protected $primaryKey = 'lad_id';

    protected $fillable = ['log_id','lad_title', 'lad_status'];
}
