<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use App\Models\Scopes\CreditSchemaTemplateScope;

class CreditSchema extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "finance.credit_schema";

    protected $primaryKey = 'cs_id';

    protected $fillable = ['cs_name', 'cs_valid', 'is_template'];

    public function creditSchemaDetail()
    {
        return $this->hasMany(CreditSchemaDetail::class, 'csd_cs_id', 'cs_id')->orderBy('csd_order')->with('creditSchemaDeadline');
    }

    public function creditSchemaPeriodPath()
    {
        return $this->hasMany(CreditSchemaPeriodPath::class, 'cs_id', 'cs_id');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CreditSchemaTemplateScope);
    }
}
