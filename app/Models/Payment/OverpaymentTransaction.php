<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;

class OverpaymentTransaction extends Model
{
    protected $table = "finance.overpayment_transaction";

    protected $primaryKey = 'ovrt_id';

    protected $fillable = [
        'ovrt_cash_in',
        'ovrt_cash_out',
        'prrb_id',
        'ovrt_remark',
        'ovrt_time',
    ];

    public $timestamps = false;
}
