<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodPath extends Model
{
    use HasFactory;

    protected $table = "masterdata.period_path";

    protected $primaryKey = 'ppd_id';

    protected $fillable = [
        'path_id','period_id','ppd_code','ppd_fee','ppd_min_grad_year','ppd_start_date','ppd_end_date'
    ];

    public function path()
    {
        return $this->belongsTo(Path::class, 'path_id','path_id');
    }

    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id','period_id')->with('schoolyear');
    }

    public function major()
    {
        return $this->hasMany(PeriodPathMajor::class, 'ppd_id','ppd_id')->with('majorLectureType');
    }
}
