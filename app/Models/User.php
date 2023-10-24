<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\HasAssociateData;
use App\Models\Payment\UserAssociateModel;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasAssociateData;

    protected $table = 'masterdata.ms_users';
    protected $primaryKey = 'user_id';
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'user_password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles()
    {
        return $this->hasMany(\App\Models\Payment\UserHasRole::class, 'user_id', 'user_id');
    }

    public function getIdAttribute()
    {
        return $this->user_id;
    }

    public function associateModels()
    {
        return $this->hasMany(UserAssociateModel::class, 'user_id', 'user_id');
    }
}
