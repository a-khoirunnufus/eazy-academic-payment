<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentBill extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "finance.payment_re_register_bill";

    protected $primaryKey = 'prrb_id';

    protected $fillable = ['prr_id', 'prrb_status', 'prrb_paid_date', 'prrb_amount', 'prrb_admin_cost', 'prrb_order', 'prrb_manual_name', 'prrb_manual_norek', 'prrb_manual_evidence', 'prrb_manual_status', 'prrb_manual_note', 'prrb_payment_method', 'prrb_due_date', 'prrb_va_number', 'prrb_order_id', 'prrb_midtrans_transaction_id', 'prrb_mandiri_bill_key', 'prrb_mandiri_biller_code', 'prrb_midtrans_transaction_exp'];
}
