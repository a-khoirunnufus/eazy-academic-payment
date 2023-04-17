<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditSchema extends Model
{
    use HasFactory;

    protected $table = "finance.credit_schema";

    protected $primaryKey = 'cs_id';

    public $timestamps = false;

    protected $fillable = ['cs_name', 'cs_valid'];

    public function creditSchemaDetail()
    {
        return $this->hasMany(CreditSchemaDetail::class, 'csd_cs_id', 'cs_id');
    }
}
