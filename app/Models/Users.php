<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    use HasFactory;
    
    protected $table = "masterdata.ms_users";

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_fullname'
    ];
}
