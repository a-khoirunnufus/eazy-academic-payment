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
    public $incrementing = false;
    protected $keyType = 'string';

    protected $appends = ['computed_admin_cost'];

    /**
     * ACCESSORS
     */

    protected function computedAdminCost(): Attribute
    {
        $admin_cost = 0;

        $record = $this->adminCost->where('mpacf_is_active', true)->first();

        if ($record) {
            if ($record->mpacf_type == 'fixed') {
                $admin_cost = $record->mpacf_fixed_fee;
            }

            if ($record->mpacf_type == 'percentage') {
                $admin_cost = $record->mpacf_percentage_fee;
            }

            if ($record->mpacf_type == 'combined') {
                $admin_cost = $record->mpacf_fixed_fee + $record->mpacf_percentage_fee;
            }
        }

        return new Attribute(get: fn () => $admin_cost);
    }

    /**
     * RELATIONSHIPS
     */

    public function adminCost()
    {
        return $this->hasMany(MasterPaymentAdminCostFinpay::class, 'mptf_code', 'mptf_code');
    }

    /**
     * SCOPES
     */

    public function scopeActive($query)
    {
        return $query->where('mptf_is_active', true);
    }
}
