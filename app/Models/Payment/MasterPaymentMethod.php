<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPaymentMethod extends Model
{
    use SoftDeletes;

    protected $table = "finance.temp_payment_method";

    protected $primaryKey = 'mpm_id';

    protected $fillable = [];

    protected static function booted()
    {
        static::addGlobalScope('active', function ($builder) {
            $builder->where('is_active', 1);
        });
    }
}
