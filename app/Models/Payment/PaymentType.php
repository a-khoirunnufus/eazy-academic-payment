<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Payment\ComponentType;

class PaymentType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "finance.payment_type_periodpath";

    protected $primaryKey = 'ptp_id';

    protected $fillable = [
        'msct_id',
        'ppm_id',
        'ptp_is_admission'
    ];

    public function type()
    {
        return $this->belongsTo(ComponentType::class, 'msct_id', 'msct_id');
    }
}
