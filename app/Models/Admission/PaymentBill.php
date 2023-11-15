<?php

namespace App\Models\Admission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentBill extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "admission.payment_re_register_bill";

    protected $primaryKey = 'prrb_id';
}
