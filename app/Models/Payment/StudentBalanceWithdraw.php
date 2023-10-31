<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use App\Models\Payment\Student;
use App\Models\User;

class StudentBalanceWithdraw extends Model
{
    protected $table = 'finance.student_balance_withdraw';

    protected $primaryKey = 'sbw_id';

    protected $fillable = [
        'student_number',
        'sbw_amount',
        'sbw_issued_by',
        'sbw_issued_time',
        'sbw_related_files',
    ];

    public $timestamps = false;

    protected $casts = [
        'sbw_related_files' => 'array',
    ];

    /**
     * RELATIONSHIPS
     */

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_number', 'student_number');
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'sbw_issued_by', 'user_id');
    }
}
