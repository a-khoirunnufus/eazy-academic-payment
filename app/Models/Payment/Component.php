<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Payment\ComponentType;

class Component extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "finance.ms_component";

    protected $primaryKey = 'msc_id';
    const CREATED_AT = 'msc_created_at';
    const UPDATED_AT = 'msc_updated_at';

    protected $fillable = [
        'msc_name','msc_description','active_status','msc_is_student','msc_is_new_student','msc_is_participant','msct_id'
    ];

    public function componentType()
    {
        return $this->belongsTo(ComponentType::class, 'msct_id', 'msct_id');
    }
}
