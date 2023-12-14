<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPaymentTypeMidtrans extends Model
{
    use SoftDeletes;

    protected $table = "finance.ms_payment_channel";

    protected $primaryKey = 'mpc_code';
    public $incrementing = false;
    protected $keyType = 'string';
}
