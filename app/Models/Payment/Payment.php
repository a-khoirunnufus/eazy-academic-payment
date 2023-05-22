<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table = "finance.payment_re_register";

    protected $primaryKey = 'prr_id';

    protected $fillable = ['reg_id', 'prr_status','prr_method','prr_total','prr_paid_net','student_number'];
}
