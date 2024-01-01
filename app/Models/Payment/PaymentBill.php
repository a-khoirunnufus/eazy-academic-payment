<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Payment\PaymentTransaction;
use App\Models\Payment\PaymentManualApproval;
use App\Enums\Payment\BillStatus;
use App\Enums\Payment\BalanceSpentStatus;

class PaymentBill extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "finance.payment_re_register_bill_new";

    protected $primaryKey = 'prrb_id';

    protected $fillable = [
        'prr_id',
        'prrb_amount',
        'prrb_admin_cost',
        'prrb_total',
        'prrb_status',
        'prrb_due_date',
        'prrb_paid_date',
        'prrb_order',
        'prrb_pg_service',
        'prrb_payment_type',
        'prrb_pg_data',
    ];


    /**
     * ACCESSORS
     */

    public function computedDispensationApplied(): Attribute
    {
        $due_date = $this->payment->dispensation
            ->sortByDesc('mds_deadline')
            ->first()
            ?->mds_deadline ?? '1970-01-01';

        return Attribute::make(
            get: fn () => strtotime($due_date) > strtotime($this->prrb_due_date),
        );
    }

    public function computedDueDate(): Attribute
    {
        $due_date = $this->payment->dispensation
            ->sortByDesc('mds_deadline')
            ->first()
            ?->mds_deadline ?? '1970-01-01';

        return Attribute::make(
            get: fn () => strtotime($due_date) > strtotime($this->prrb_due_date) ? $due_date : $this->prrb_due_date,
        );
    }

    public function computedNominalPaid(): Attribute
    {
        $paid_amount = 0;

        if ($this->paymentTransaction->isNotEmpty()) {
            foreach ($this->paymentTransaction as $transaction) {
                $paid_amount += $transaction->computed_nett_amount;
            }
        }

        return Attribute::make(get: fn () => $paid_amount);
    }

    public function computedNominalPaidNett(): Attribute
    {
        $paid_amount = 0;

        if ($this->paymentTransaction->isNotEmpty()) {
            foreach ($this->paymentTransaction as $transaction) {
                $paid_amount += $transaction->computed_nett_amount;
            }
        }

        return Attribute::make(get: fn () => $paid_amount);
    }

    public function computedNominalPaidGross(): Attribute
    {
        $paid_amount = 0;

        if ($this->paymentTransaction->isNotEmpty()) {
            foreach ($this->paymentTransaction as $transaction) {
                $paid_amount += $transaction->computed_gross_amount;
            }
        }

        return Attribute::make(get: fn () => $paid_amount);
    }

    public function computedIsFullyPaid(): Attribute
    {
        $paid_amount = $this->computed_nominal_paid_nett;
        $total_discount = $this->studentBalanceSpent
            ->where('sbs_status', BalanceSpentStatus::Used->value)
            ->sum('sbs_amount');

        return Attribute::make(
            get: fn () => $paid_amount >= ($this->prrb_amount - $total_discount),
        );
    }

    public function computedPaymentStatus(): Attribute
    {
        $get_func = fn () => BillStatus::NotPaidOff->value;

        if ($this->computed_dispensation_applied) {
            $get_func = fn () => BillStatus::Credit->value;
        }

        if ($this->computed_is_fully_paid) {
            $get_func = fn () => BillStatus::PaidOff->value;
        }

        return Attribute::make(get: $get_func);
    }

    public function computedPaidDate(): Attribute
    {
        $latest_trans = $this->paymentTransaction->sortByDesc('prrt_time')->first();

        return Attribute::make(get: fn () => $latest_trans?->prrt_time ?? null);
    }

    public function computedStudentBalanceSpendTotal(): Attribute
    {
        $amount = $this->studentBalanceSpent->sum('sbs_amount');

        return Attribute::make(get: fn () => $amount);
    }




    /**
     * RELATIONSHIPS
     */

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'prr_id', 'prr_id');
    }

    // public function paymentMethod()
    // {
    //     return $this->hasOne(PaymentMethod::class, 'mpm_key', 'prrb_payment_method');
    // }

    public function paymentManualApproval()
    {
        return $this->hasMany(PaymentManualApproval::class, 'prrb_id', 'prrb_id');
    }

    public function paymentTransaction()
    {
        return $this->hasMany(PaymentTransaction::class, 'prrb_id', 'prrb_id');
    }

    public function studentBalanceSpent()
    {
        return $this->hasMany(StudentBalanceSpent::class, 'prrb_id', 'prrb_id');
    }
}
