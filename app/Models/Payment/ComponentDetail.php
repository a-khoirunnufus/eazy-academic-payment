<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Path;
use App\Models\Period;
use App\Models\lectureType;

class ComponentDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    
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
    
    public function path()
    {
        return $this->belongsTo(Path::class, 'path_id','path_id');
    }
    
    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id','period_id');
    }
    
    public function lectureType()
    {
        return $this->belongsTo(lectureType::class, 'mlt_id','mlt_id');
    }
}
												