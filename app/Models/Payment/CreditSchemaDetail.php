<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditSchemaDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "finance.credit_schema_detail";

    protected $primaryKey = 'csd_id';

    protected $fillable = ['csd_cs_id', 'csd_percentage', 'csd_order'];

    public function creditSchemaDeadline()
    {
        return $this->belongsTo(CreditSchemaDeadline::class, 'csd_id','csd_id');
    }
}
