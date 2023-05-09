<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditSchemaPeriodPath extends Model
{
    use HasFactory;

    protected $table = "finance.credit_schema_periodpath";

    protected $primaryKey = 'cspp_id';

    protected $fillable = ['cs_id', 'ppm_id'];
    
    public function creditSchema()
    {
        return $this->belongsTo(CreditSchema::class, 'cs_id','cs_id');
    }
}