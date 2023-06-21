<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Year;

class Discount extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table = "finance.ms_discount";

    protected $primaryKey = 'md_id';

    protected $fillable = ['md_name', 'md_period_start', 'md_period_end','md_nominal','md_budget','md_realization','md_status'];
    
    public function periodStart()
    {
        return $this->belongsTo(Year::class, 'md_period_start','msy_id');
    }
    
    public function periodEnd()
    {
        return $this->belongsTo(Year::class, 'md_period_end','msy_id');
    }
}
