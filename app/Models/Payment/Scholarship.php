<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Payment\Year;

class Scholarship extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "finance.ms_scholarship";

    protected $primaryKey = 'ms_id';

    protected $fillable = ['ms_name','ms_type','ms_from','ms_from_name','ms_from_phone','ms_from_email', 'ms_period_start', 'ms_period_end','ms_nominal','ms_budget','ms_realization','ms_status'];

    public function periodStart()
    {
        return $this->belongsTo(Year::class, 'ms_period_start','msy_id');
    }

    public function periodEnd()
    {
        return $this->belongsTo(Year::class, 'ms_period_end','msy_id');
    }
}
