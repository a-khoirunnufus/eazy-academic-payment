<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentManualApproval extends Model
{
    use SoftDeletes;

    protected $table = "finance.payment_manual_approval";

    protected $primaryKey = 'pma_id';

    protected $fillable = [
        'prrb_id',
        'pma_sender_account_name',
        'pma_sender_account_number',
        'pma_sender_bank',
        'pma_amount',
        'pma_evidence',
        'pma_approval_status',
        'pma_notes',
        'pma_receiver_account_name',
        'pma_receiver_account_number',
        'pma_receiver_bank',
        'pma_payment_time',
        'pma_student_id',
        'pma_student_name',
        'pma_student_type',
        'pma_student_studyprogram',
        'pma_student_lecturetype',
        'pma_student_reg_id',
        'pma_student_reg_year',
        'pma_student_reg_period',
        'pma_student_reg_path',
    ];
}
