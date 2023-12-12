<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPaymentAdminCostMidtrans extends Model
{
    use SoftDeletes;

    protected $table = "finance.ms_payment_admin_cost_midtrans";

    protected $primaryKey = 'mpacm_id';
}
