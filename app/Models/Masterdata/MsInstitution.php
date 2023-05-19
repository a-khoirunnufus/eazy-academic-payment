<?php

namespace App\Models\Masterdata;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MsInstitution extends Model
{
    public static $defaultInstitutionId = 7;

    protected $table = "masterdata.ms_institution";

    protected $primaryKey = 'institution_id';

    public function faculties(): HasMany
    {
        return $this->hasMany(MsFaculty::class, 'institution_id', 'institution_id');
    }
}
