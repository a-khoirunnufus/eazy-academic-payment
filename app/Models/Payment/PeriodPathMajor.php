<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payment\CreditSchemaPeriodPath;
use App\Models\Payment\componentDetail;

class PeriodPathMajor extends Model
{
    use HasFactory;

    protected $table = "masterdata.period_path_major";

    protected $primaryKey = 'ppm_id';

    protected $fillable = [
        'ppd_id','mma_lt_id'
    ];

    public function majorLectureType()
    {
        return $this->belongsTo(MajorLectureType::class, 'mma_lt_id','mma_lt_id')->with('studyProgram','lectureType');
    }

    public function credit()
    {
        return $this->hasMany(CreditSchemaPeriodPath::class, 'ppm_id','ppm_id')->with('creditSchema');
    }

    public function periodPath()
    {
        return $this->belongsTo(PeriodPath::class, 'ppd_id','ppd_id')->with('period');
    }

}
