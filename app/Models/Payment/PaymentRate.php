<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Payment\PaymentCredit;
use App\Models\Payment\PaymentComponent;
use App\Models\Path;
use App\Models\Period;
use App\Models\Studyprogram;

class PaymentRate extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table = "finance.fee";

    protected $primaryKey = 'f_id';

    protected $fillable = [
        'f_period_id','f_path_id','f_studyprogram_id','f_jenis_perkuliahan_id'
    ];

    public function component()
    {
        return $this->hasMany(PaymentComponent::class, 'f_id','f_id')->orderBy('f_id','asc')->with('componentDetail');
    }

    public function credit()
    {
        return $this->hasMany(PaymentCredit::class, 'f_id','f_id')->with('creditSchema');
    }
    
    public function path()
    {
        return $this->belongsTo(Path::class, 'f_path_id','path_id');
    }
    
    public function period()
    {
        return $this->belongsTo(Period::class, 'f_period_id','period_id')->with('schoolyear');
    }
    
    public function studyProgram()
    {
        return $this->belongsTo(Studyprogram::class, 'f_studyprogram_id','studyprogram_id');
    }
}
