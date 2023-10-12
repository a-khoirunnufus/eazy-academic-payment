<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $table = "finance.ms_settings";

    protected $primaryKey = 'ms_id';

    protected $fillable = [
        'name','value'
    ];
}
