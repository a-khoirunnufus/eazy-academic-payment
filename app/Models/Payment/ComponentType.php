<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComponentType extends Model
{
    use HasFactory;
    
    protected $table = "finance.ms_component_type";

    protected $primaryKey = 'msct_id';

    protected $fillable = [
        'msct_name'
    ];

}
