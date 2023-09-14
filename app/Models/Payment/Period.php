<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payment\Year;

class Period extends Model
{
    use HasFactory;

    protected $table = "masterdata.ms_period";

    protected $primaryKey = 'period_id';

    protected $fillable = [
        'msy_id','period_name','period_start','period_end','period_status'
    ];

    public function schoolyear()
    {
        return $this->belongsTo(Year::class, 'msy_id','msy_id');
    }
}
