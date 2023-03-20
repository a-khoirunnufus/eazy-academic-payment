<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Models\HasUuid;
use App\Traits\Models\HasResource;

class Resource extends Model
{
    use HasUuid, HasResource;
    
    protected $fillable = [
        'filepath'
    ];

    protected $resourceColumn = [
        'filepath'
    ];
}
