<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Users;

class LogActivity extends Model
{
    use HasFactory;

    protected $table = "finance.log_activity";

    protected $primaryKey = 'log_id';

    protected $fillable = ['log_activity', 'user_id', 'log_status', 'log_route','log_route_parameter'];

    public function detail()
    {
        return $this->hasMany(LogActivityDetail::class, 'log_id', 'log_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id','user_id');
    }
}
