<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComponentDetail extends Model
{
    use HasFactory;
    
    protected $table = "finance.component_detail";

    protected $primaryKey = 'cd_id';
    const CREATED_AT = 'cd_created_at';
    const UPDATED_AT = 'cd_updated_at';
    
    protected $fillable = [
        'mma_id','msc_id','period_id','path_id','cd_fee','msy_id','mlt_id','cd_created_by','cd_is_package','ppm_id'
    ];
    
    public function component()
    {
        return $this->belongsTo(Component::class, 'msc_id','msc_id');
    }
}
												