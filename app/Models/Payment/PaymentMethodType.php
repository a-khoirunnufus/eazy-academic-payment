<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class PaymentMethodType extends Model
{
    use SoftDeletes;

    protected $table = "finance.ms_payment_method_type";

    protected $fillable = [];

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class, 'mpm_type', 'code');
    }
}
