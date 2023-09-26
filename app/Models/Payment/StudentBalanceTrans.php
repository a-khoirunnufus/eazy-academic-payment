<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use App\Models\Payment\StudentBalanceTransType as Type;

class StudentBalanceTrans extends Model
{
    protected $table = 'finance.student_balance_trans';

    protected $primaryKey = 'sbt_id';

    protected $fillable = [
        'student_number',
        'sbt_opening_balance',
        'sbt_amount',
        'sbtt_name',
        'sbtt_associate_id',
        'sbt_closing_balance',
        'sbt_time',
    ];

    public $timestamps = false;

    public function type()
    {
        return $this->belongsTo(Type::class, 'sbtt_name', 'sbtt_name');
    }

    public function getAssociateData()
    {
        $className = $this->type->sbtt_associate_model;

        return $className::find($this->sbtt_associate_id);
    }
}
