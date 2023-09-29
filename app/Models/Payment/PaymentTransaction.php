<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Payment\PaymentMethod;
use App\Models\Payment\StudentBalanceTrans;
use App\Enums\Payment\BalanceTransType;

class PaymentTransaction extends Model
{
    use SoftDeletes;

    protected $table = "finance.payment_re_register_transaction";

    protected $primaryKey = 'prrt_id';

    protected $fillable = [
        'prrb_id',
        'prrt_payment_method',
        'prrt_va_number',
        'prrt_mandiri_bill_key',
        'prrt_sender_account_number',
        'prrt_receiver_account_number',
        'prrt_amount',
        'prrt_time',
    ];

    /**
     * ACCESSORS
     */

    public function computedInitialAmount(): Attribute
    {
        $total_overpayment = $this->overpayment()->sum('sbt_amount');

        return Attribute::make(
            get: fn () => $this->prrt_amount + $total_overpayment,
        );
    }

    public function computedOverpayment(): Attribute
    {
        $overpayments = $this->overpayment()
            ->where('sbtt_name', BalanceTransType::OverpaidBill)
            ->get();

        return Attribute::make(
            get: fn () => $overpayments,
        );
    }

    /**
     * RELATIONS
     */

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'prrt_payment_method', 'mpm_key');
    }

    public function overpayment()
    {
        return $this->hasMany(StudentBalanceTrans::class, 'sbtt_associate_id', 'prrt_id');
    }
}
