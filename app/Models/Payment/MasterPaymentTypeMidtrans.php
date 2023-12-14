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
    public $incrementing = false;
    protected $keyType = 'string';

    protected $appends = ['computed_admin_cost'];

    /**
     * ACCESSORS
     */

    protected function computedAdminCost(): Attribute
    {
        $admin_cost = 0;

        $record = $this->adminCost->where('mpacm_is_active', true)->first();

        if ($record) {
            if ($record->mpacm_type == 'fixed') {
                $admin_cost = $record->mpacm_fixed_fee;
            }

            if ($record->mpacm_type == 'percentage') {
                $admin_cost = $record->mpacm_percentage_fee;
            }

            if ($record->mpacm_type == 'combined') {
                $admin_cost = $record->mpacm_fixed_fee + $record->mpacm_percentage_fee;
            }
        }

        return new Attribute(get: fn () => $admin_cost);
    }

    /**
     * RELATIONSHIPS
     */

    public function adminCost()
    {
        return $this->hasMany(MasterPaymentAdminCostMidtrans::class, 'mptm_code', 'mptm_code');
    }

    /**
     * SCOPES
     */

    public function scopeActive($query)
    {
        return $query->where('mptm_is_active', true);
    }
}
