<?php

namespace App\Models\PMB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRegister extends Model
{
    protected $table = 'pmb.payment_register';
    protected $primaryKey = 'payment_reg_id';

    public function PaymentRegisterDetail(){
        return $this->hasMany(PaymentRegisterDetail::class, 'payment_reg_id', 'payment_reg_id');
    }
}
