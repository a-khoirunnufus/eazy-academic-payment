<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPaymentTypeMidtrans extends Model
{
    use SoftDeletes;

    protected $table = "finance.ms_payment_method_new";

    protected $primaryKey = 'mpm_code';
}
