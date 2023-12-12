<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Payment\MasterPaymentAdminCostMidtrans;

class MasterPaymentTypeMidtrans extends Model
{
    use SoftDeletes;

    protected $table = "finance.ms_payment_type_midtrans";

    protected $primaryKey = 'mptm_code';

    protected $appends = ['computed_admin_cost'];

    /**
     * ACCESSORS
     */

    protected function computedAdminCost(): Attribute
    {
        $admin_cost = $this->adminCost->where('mpacm_is_active', true)->first();

        return new Attribute(get: fn () => $admin_cost ?? 0);
    }

    /**
     * RELATIONSHIPS
     */

    public function adminCost()
    {
        return $this->hasMany(MasterPaymentAdminCostMidtrans::class, 'mptm_code', 'mptm_code');
    }
}
