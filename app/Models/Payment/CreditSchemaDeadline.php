<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditSchemaDeadline extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table = "finance.credit_schema_deadline";

    protected $primaryKey = 'cse_id';

    protected $fillable = ['cs_id', 'csd_id','cse_deadline'];

}
