<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPayeeAccount extends Model
{
    use SoftDeletes;

    protected $table = "finance.ms_payee_account";

    protected $primaryKey = 'mpa_id';
}
