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

    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $appends = ['computed_admin_cost'];

    /**
     * ACCESSORS
     */

    protected function computedAdminCost(): Attribute
    {
        $admin_cost = 0;

        $record = $this->adminCost->where('is_active', true)->first();

        if ($record) {
            if ($record->type == 'fixed') {
                $admin_cost = $record->fixed_fee;
            }

            if ($record->type == 'percentage') {
                $admin_cost = $record->percentage_fee;
            }

            if ($record->type == 'combined') {
                $admin_cost = $record->fixed_fee + $record->percentage_fee;
            }
        }

        return new Attribute(get: fn () => $admin_cost);
    }

    /**
     * RELATIONSHIPS
     */

    public function adminCost()
    {
        return $this->hasMany(MasterPaymentAdminCostMidtrans::class, 'payment_type_code', 'code');
    }

    /**
     * SCOPES
     */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
