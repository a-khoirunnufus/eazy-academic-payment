<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GraduationReceiver extends Model
{
    use HasFactory;

    protected $table = "finance.ms_graduation_receiver";

    protected $primaryKey = 'mgr_id';

    protected $fillable = [
        'student_number','msy_code','mgr_nominal','mgr_status','prr_id'
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'prr_id','prr_id');
    }

    public function year()
    {
        return $this->belongsTo(Year::class, 'msy_code','msy_code');
    }

    public function student()
    {
        return $this->belongsTo(MsStudent::class, 'student_number', 'student_number')->with('getComponent');
    }
}