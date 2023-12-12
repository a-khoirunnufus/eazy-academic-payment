<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Payment\MasterPaymentAdminCostFinpay;

class MasterPaymentTypeFinpay extends Model
{
    use SoftDeletes;

    protected $table = "finance.ms_payment_type_finpay";

    protected $primaryKey = 'mptf_code';

    protected $appends = ['computed_admin_cost'];

    /**
     * ACCESSORS
     */

    protected function computedAdminCost(): Attribute
    {
        $admin_cost = $this->adminCost->where('mpacf_is_active', true)->first();

        return new Attribute(get: fn () => $admin_cost ?? 0);
    }

    /**
     * RELATIONSHIPS
     */

    public function adminCost()
    {
        return $this->hasMany(MasterPaymentAdminCostFinpay::class, 'mptf_code', 'mptf_code');
    }
}
