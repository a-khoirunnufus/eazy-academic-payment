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

    protected $fillable = ['prr_id', 'prrb_status','prrb_invoice_num','prrb_expired_date','prrb_resync_date','prrb_resync_by','prrb_paid_date','prrb_qr_code','prrb_mt_id','prrb_amount','prrb_admin_cost'];
}
