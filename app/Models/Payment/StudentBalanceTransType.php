<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;

class StudentBalanceTransType extends Model
{
    protected $table = 'finance.student_balance_trans_type';

    protected $primaryKey = 'sbtt_name';
    public $incrementing = false;
    public $keyType = 'string';

    protected $fillable = [
        'sbtt_name',
        'sbtt_is_cash_in',
        'sbtt_associate_model',
        'sbtt_description',
    ];

    public $timestamps = false;
}
