<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Year;
use App\Models\Student;
use App\Models\Payment\Discount;

class DiscountReceiver extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table = "finance.ms_discount_receiver";

    protected $primaryKey = 'mdr_id';

    protected $fillable = ['md_id', 'student_number', 'mdr_period','mdr_nominal','mdr_status'];
    
    public function discount()
    {
        return $this->belongsTo(Discount::class, 'md_id','md_id')->withTrashed();
    }

    public function period()
    {
        return $this->belongsTo(Year::class, 'mdr_period','msy_id');
    }
    
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_number','student_number')->with('studyProgram');
    }
}
