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
use App\Models\Student\DispensationSubmission;
use App\Enums\Payment\BillStatus;

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

    public function computedPaymentStatus(): Attribute
    {
        $get_func = fn () => BillStatus::NotPaidOff;

        $due_date = $this->dispensation()
            ->orderBy('mds_deadline', 'desc')
            ->first()
            ?->mds_deadline ?? '1970-01-01';
        $dispensation_applied = strtotime($due_date) > time();
        if ($dispensation_applied) $get_func = fn () => BillStatus::Credit;

        $fully_paid = $this->payment_bill && $this->paymentBill()
            ->whereIn('prrb_status', [BillStatus::NotPaidOff, BillStatus::Credit])
            ->doesntExist();
        if ($fully_paid) $get_func = fn () => BillStatus::PaidOff;

        return Attribute::make(get: $get_func);
    }

    public function computedHasPaidBill(): Attribute
    {
        $has_paid_bill = false;
        if ($this->payment_bill) {
            foreach ($this->payment_bill as $bill) {
                if ($bill->payment_transaction) {
                    $has_paid_bill = true;
                    break;
                }
            }
        }

        return Attribute::make(
            get: fn () => $has_paid_bill,
        );
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

    public function paymentMethod()
    {
        return $this->hasOne(PaymentMethod::class, 'mpm_key', 'prr_method');
    }

    public function dispensation()
    {
        return $this->hasMany(DispensationSubmission::class, 'prr_id', 'prr_id');
    }
}
