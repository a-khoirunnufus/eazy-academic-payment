<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditSchemaDetail extends Model
{
    use HasFactory;

    protected $table = "finance.credit_schema_detail";

    protected $primaryKey = 'csd_id';

    protected $fillable = ['csd_cs_id', 'csd_percentage', 'csd_order'];
}
