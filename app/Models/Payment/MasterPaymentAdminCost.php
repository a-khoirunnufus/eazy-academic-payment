<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPaymentAdminCost extends Model
{
    use SoftDeletes;

    protected $table = "finance.ms_payment_admin_cost";

    protected $primaryKey = 'id';

    /**
     * SCOPES
     */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
