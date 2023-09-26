<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use App\Models\Payment\StudentBalanceTransType as Type;
use App\Models\Payment\PaymentBill;

class StudentBalanceSpent extends Model
{
    protected $table = 'finance.student_balance_spent';

    protected $primaryKey = 'sbs_id';

    protected $fillable = [
        'student_number',
        'sbs_amount',
        'sbs_remark',
        'prrb_id',
        'sbs_time',
        'sbs_status',
    ];

    public $timestamps = false;

    public function bill()
    {
        return $this->belongsTo(PaymentBill::class, 'prrb_id', 'prrb_id');
    }
}
