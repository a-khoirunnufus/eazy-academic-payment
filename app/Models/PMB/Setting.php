<?php

namespace App\Models\PMB;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'pmb.setting';
    protected $primaryKey = 'setting_id';

    const CREATED_AT = 'setting_input_date';
    const UPDATED_AT = 'setting_update_date';
    const DELETED_AT = 'setting_delete_date';
}
