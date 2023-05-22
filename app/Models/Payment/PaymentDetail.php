<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table = "finance.payment_re_register_detail";

    protected $primaryKey = 'prrd_id';

    protected $fillable = ['prr_id', 'prrd_component','prrd_amount'];
}
