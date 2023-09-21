<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Users;

class MasterJob extends Model
{
    use HasFactory;
    
    protected $table = "finance.ms_jobs";

    protected $primaryKey = 'mj_id';

    protected $fillable = ['queue', 'user_id', 'status'];
    
    public function detail()
    {
        return $this->hasMany(MasterJobDetail::class, 'mj_id', 'mj_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id','user_id');
    }
}
