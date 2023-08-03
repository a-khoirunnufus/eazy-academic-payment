<?php

namespace App\Models\PMB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRegisterDetail extends Model
{
    protected $table = 'pmb.payment_register_detail';
    protected $primaryKey = 'payment_rd_id';
}
