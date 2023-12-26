<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPaymentMethodNew extends Model
{
    use SoftDeletes;

    protected $table = "finance.ms_payment_method_new";

    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';
}
