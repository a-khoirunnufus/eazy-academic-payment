<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Payment\MasterPaymentAdminCostManual;

class MasterPaymentTypeManual extends Model
{
    use SoftDeletes;

    protected $table = "finance.ms_payment_type_manual";

    protected $primaryKey = 'mptman_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $appends = ['computed_admin_cost'];

    /**
     * ACCESSORS
     */

    protected function computedAdminCost(): Attribute
    {
        $admin_cost = 0;

        $record = $this->adminCost->where('mpacman_is_active', true)->first();

        if($record) {
            if ($record->mpacman_type == 'fixed') {
                $admin_cost = $record->mpacman_fixed_fee;
            }

            if ($record->mpacman_type == 'percentage') {
                $admin_cost = $record->mpacman_percentage_fee;
            }

            if ($record->mpacman_type == 'combined') {
                $admin_cost = $record->mpacman_fixed_fee + $record->mpacman_percentage_fee;
            }
        }

        return new Attribute(get: fn () => $admin_cost);
    }

    /**
     * RELATIONSHIPS
     */

    public function adminCost()
    {
        return $this->hasMany(MasterPaymentAdminCostManual::class, 'mptman_code', 'mptman_code');
    }

    /**
     * SCOPES
     */

    public function scopeActive($query)
    {
        return $query->where('mptman_is_active', true);
    }
}
