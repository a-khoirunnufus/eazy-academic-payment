<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Path extends Model
{
    use HasFactory;
    
    protected $table = "masterdata.ms_path";

    protected $primaryKey = 'path_id';

    protected $fillable = [
        'mpt_id','path_name','path_fee','path_min_edu','mlt_id','path_description'
    ];

}
