<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPaymentAdminCostFinpay extends Model
{
    use SoftDeletes;

    protected $table = "finance.ms_payment_admin_cost_finpay";

    protected $primaryKey = 'id';

    /**
     * SCOPES
     */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
