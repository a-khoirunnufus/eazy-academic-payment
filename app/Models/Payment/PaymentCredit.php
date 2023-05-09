<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Payment\CreditSchema;

class PaymentCredit extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table = "finance.fee_credit";

    protected $primaryKey = 'fc_id';

    protected $fillable = [
        'f_id','cs_id'
    ];
    
    public function creditSchema()
    {
        return $this->belongsTo(CreditSchema::class, 'cs_id','cs_id');
    }
}
