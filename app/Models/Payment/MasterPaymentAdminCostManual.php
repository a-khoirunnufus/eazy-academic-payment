<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPaymentAdminCostManual extends Model
{
    use SoftDeletes;

    protected $table = "finance.ms_payment_admin_cost_manual";

    protected $primaryKey = 'id';

    /**
     * SCOPES
     */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
