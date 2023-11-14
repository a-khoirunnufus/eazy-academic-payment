<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payment\Payment;
use App\Models\HR\MsStudent;
use App\Models\Payment\Year;

class LeaveReceiver extends Model
{
    use HasFactory;

    protected $table = "finance.ms_leave_receiver";

    protected $primaryKey = 'mlr_id';

    protected $fillable = [
        'student_number','msy_id','mlr_nominal','mlr_status','prr_id'
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'prr_id','prr_id');
    }

    public function year()
    {
        return $this->belongsTo(Year::class, 'msy_id','msy_id');
    }

    public function student()
    {
        return $this->belongsTo(MsStudent::class, 'student_number', 'student_number')->with('getComponent');
    }
}
