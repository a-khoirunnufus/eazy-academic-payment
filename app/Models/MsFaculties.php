<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Models\Scopes\Faculty as FacultyScope;

class MsFaculties extends Model
{
    use SoftDeletes, FacultyScope;
    //use LogsActivity;

    protected $table = 'masterdata.ms_faculties';
    protected $primaryKey = 'faculty_id';
    // protected $dates = ['deleted_at'];
    // public $sequence = 'iclia.ms_spk_id_seq';

    protected $fillable = [
        'faculty_name',
        'faculty_name_english',
        'active_status',
        'faculty_singkatan',
        'faculty_emp_id',
    ];

    //protected static $logName = 'iclia.iclia_ms_spk';
    //protected static $logAttributes = ['*'];

    public static function getNextSequenceId()
    {
        $next_id = DB::select("select nextval('masterdata.ms_faculties_faculty_id_seq')");
        return intval($next_id['0']->nextval);
    }

    public function prodi()
    {
        return $this->belongsTo(StudyProgram::class, 'faculty_id');
    }
    
    public function studyprograms()
    {
        return $this->hasMany(StudyProgram::class, 'faculty_id', 'faculty_id');
    }

    public function workLocation()
    {
        return $this->belongsTo(LokasiKerja::class, 'faculty_id', 'worklocation_faculty_id');
    }

    public function institution()
    {
        return $this->hasOne(Institution::class, 'institution_id', 'institution_id');
    }

    public function kelompokkeahlian()
    {
        return $this->belongsTo(KelompokKeahlian::class, 'faculty_id', 'skill_group_id');
    }

    public function courseCrosses()
    {
        return $this->hasMany(CourseCross::class, 'faculty_id', 'faculty_id');
    }
}
