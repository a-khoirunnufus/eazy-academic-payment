<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class MasterPaymentMethodType extends Model
{
    use SoftDeletes;

    protected $table = "finance.temp_payment_method_type";

    protected $fillable = [];

    public function paymentMethods()
    {
        return $this->hasMany(MasterPaymentMethod::class, 'mpm_type', 'code');
    }
}
