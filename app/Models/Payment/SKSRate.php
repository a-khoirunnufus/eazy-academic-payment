<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Payment\Studyprogram;

class SKSRate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "finance.ms_sks_rates";

    protected $primaryKey = 'msr_id';

    protected $fillable = [
        'msr_studyprogram_id','msr_tingkat','msr_rate','msr_rate_practicum','msr_active_status'
    ];

    public function studyProgram()
    {
        return $this->belongsTo(Studyprogram::class, 'msr_studyprogram_id','studyprogram_id');
    }
}
