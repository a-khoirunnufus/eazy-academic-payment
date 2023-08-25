<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTransaction extends Model
{
    use SoftDeletes;

    protected $table = "finance.payment_re_register_transaction";

    protected $primaryKey = 'prrt_id';

    protected $fillable = [
        'prrb_id',
        'prrt_payment_method',
        'prrt_va_number',
        'prrt_mandiri_bill_key',
        'prrt_account_number',
        'prrt_amount',
        'prrt_time',
    ];
}
