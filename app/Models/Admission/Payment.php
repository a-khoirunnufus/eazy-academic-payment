<?php

namespace App\Models\Admission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Admission\PaymentBill;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "admission.payment_re_register";

    protected $primaryKey = 'prr_id';

    /**
     * RELATIONS
     */

    public function paymentBill()
    {
        return $this->hasMany(PaymentBill::class, 'prr_id', 'prr_id');
    }
}
