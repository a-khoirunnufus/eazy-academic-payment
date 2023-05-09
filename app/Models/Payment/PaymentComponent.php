<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Payment\Component;

class PaymentComponent extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table = "finance.fee_component";

    protected $primaryKey = 'fc_id';

    protected $fillable = [
        'fc_id','f_id','msc_id','fc_rate'
    ];
    
    public function componentDetail()
    {
        return $this->belongsTo(Component::class, 'msc_id','msc_id');
    }
}
