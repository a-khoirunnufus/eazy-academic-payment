<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PMB\Register;
use App\Models\HR\MsStudent;
use App\Models\Year;

class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "finance.payment_re_register";

    protected $primaryKey = 'prr_id';

    protected $fillable = ['reg_id', 'prr_status','prr_method','prr_total','prr_paid_net','student_number','prr_school_year', 'par_id'];

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
        return $this->hasOne(Register::class, 'reg_id', 'reg_id');
    }

    public function student()
    {
        return $this->hasOne(MsStudent::class, 'student_number', 'student_number');
    }

    public function year()
    {
        return $this->hasOne(Year::class, 'msy_code', 'prr_school_year');
    }
}
