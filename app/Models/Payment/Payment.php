<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PMB\Register;
use App\Models\HR\MsStudent;
use App\Models\Payment\Year;
use App\Models\Payment\PaymentMethod;
use App\Models\Payment\DispensationSubmission;
use App\Enums\Payment\BillStatus;
use App\Enums\Payment\PaymentDetailType as DetailType;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "finance.payment_re_register";

    protected $primaryKey = 'prr_id';

    protected $fillable = [
        'reg_id',
        'prr_status',
        'prr_total',
        'prr_paid_net',
        'student_number',
        'prr_school_year',
        'par_id',
        'prr_dispensation_date'
    ];

    /**
     * ACCESSORS
     */

    public function computedTotalBill(): Attribute
    {
        $total_bill = $this->paymentDetail()->sum('prrd_amount');

        return Attribute::make(
            get: fn () => $total_bill,
        );
    }

    public function computedFinalBill(): Attribute
    {
        $total_bill = $this->computed_component_total_amount - ($this->computed_discount_total_amount + $this->computed_scholarship_total_amount);
        return Attribute::make(get: fn () => $total_bill);
    }

    public function computedDispensationApplied(): Attribute
    {
        $due_date = $this->dispensation
            ->sortByDesc('mds_deadline')
            ->first()
            ?->mds_deadline ?? '1970-01-01';

        $dispensation_applied = strtotime($due_date) > time();

        return Attribute::make(get: fn () => $dispensation_applied);
    }

    public function computedCreditApplied(): Attribute
    {
        $credit_applied = $this->credit->isNotEmpty();
        return Attribute::make(get: fn () => $credit_applied);
    }

    public function computedIsFullyPaid(): Attribute
    {
        $fully_paid = $this->paymentBill->isNotEmpty() && $this->paymentBill
            ->whereIn('prrb_status', [BillStatus::NotPaidOff->value, BillStatus::Credit->value])
            ->isEmpty();

        return Attribute::make(get: fn () => $fully_paid);
    }

    public function computedPaymentStatus(): Attribute
    {
        $get_func = fn () => BillStatus::NotPaidOff->value;

        if (
            $this->computed_dispensation_applied
            or $this->computed_credit_applied
        ) {
            $get_func = fn () => BillStatus::Credit->value;
        }

        if ($this->computed_is_fully_paid) {
            $get_func = fn () => BillStatus::PaidOff->value;
        }

        return Attribute::make(get: $get_func);
    }

    public function computedHasPaidBill(): Attribute
    {
        $has_paid_bill = false;
        if ($this->paymentBill->isNotEmpty()) {
            foreach ($this->paymentBill as $bill) {
                if ($bill->paymentTransaction->isNotEmpty()) {
                    $has_paid_bill = true;
                    break;
                }
            }
        }

        return Attribute::make(get: fn () => $has_paid_bill);
    }

    public function computedComponentList(): Attribute
    {
        $items = $this->paymentDetail->where('type', DetailType::Component->value);
        return Attribute::make(get: fn () => $items);
    }

    public function computedComponentTotalAmount(): Attribute
    {
        $amount = $this->paymentDetail->where('type', DetailType::Component->value)->sum('prrd_amount');
        return Attribute::make(get: fn () => $amount);
    }

    public function computedDiscountList(): Attribute
    {
        $items = $this->paymentDetail->where('type', DetailType::Discount->value);
        return Attribute::make(get: fn () => $items);
    }

    public function computedDiscountTotalAmount(): Attribute
    {
        $amount = $this->paymentDetail->where('type', DetailType::Discount->value)->sum('prrd_amount');
        return Attribute::make(get: fn () => $amount);
    }

    public function computedScholarshipList(): Attribute
    {
        $items = $this->paymentDetail->where('type', DetailType::Scholarship->value);
        return Attribute::make(get: fn () => $items);
    }

    public function computedScholarshipTotalAmount(): Attribute
    {
        $amount = $this->paymentDetail->where('type', DetailType::Scholarship->value)->sum('prrd_amount');
        return Attribute::make(get: fn () => $amount);
    }

    /**
     * RELATIONS
     */

    public function paymentDetail()
    {
        return $this->hasMany(PaymentDetail::class, 'prr_id', 'prr_id')->orderBy('prrd_id', 'asc');
    }

    public function paymentBill()
    {
        return $this->hasMany(PaymentBill::class, 'prr_id', 'prr_id')->orderBy('prrb_id', 'asc');
    }

    public function register()
    {
        return $this->hasOne(Register::class, 'reg_id', 'reg_id')->with('participant');
    }

    public function student()
    {
        return $this->hasOne(MsStudent::class, 'student_number', 'student_number')->with('getComponent');
    }

    public function year()
    {
        return $this->hasOne(Year::class, 'msy_code', 'prr_school_year');
    }

    // pengajuan dispensasi yang diapprove
    public function dispensation()
    {
        return $this->hasMany(DispensationSubmission::class, 'prr_id', 'prr_id');
    }

    // pengajuan cicilan yang diapprove
    public function credit()
    {
        return $this->hasMany(CreditSubmission::class, 'prr_id', 'prr_id');
    }
}
