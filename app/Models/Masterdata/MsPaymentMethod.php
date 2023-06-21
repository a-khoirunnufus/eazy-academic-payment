<?php

namespace App\Models\Masterdata;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MsPaymentMethod extends Model
{
    use SoftDeletes;

    protected $table = 'masterdata.ms_payment_method';
    protected $primaryKey = 'mpm_id';
}
