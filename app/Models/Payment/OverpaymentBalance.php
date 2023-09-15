<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;

class OverpaymentBalance extends Model
{
    protected $table = "finance.overpayment_balance";

    protected $primaryKey = 'ovrb_id';

    protected $fillable = [
        'student_id',
        'participant_id',
        'student_type',
        'ovrb_balance',
    ];
}
