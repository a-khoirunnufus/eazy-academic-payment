<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class MsEmployee extends Model
{
    protected $table = 'hr.ms_employee';
    protected $primaryKey = 'emp_num';
    protected $appends = ['fullname_with_title'];

    public static function getNextSequenceId()
    {
        $next_id = DB::select("select nextval('hr.ms_employee_emp_num_seq')");
        return intval($next_id['0']->nextval);
    }

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_nip', 'emp_id');
    }

    public function workLocation()
    {
        return $this->hasOne(LokasiKerja::class, 'worklocation_id', 'work_location');
    }

    public function education_histories()
    {
        return $this->hasMany(EmployeeEducationHistory::class, 'emp_num', 'emp_num');
    }

    public function course()
    {
        return $this->hasOne(Course::class, 'emp_num');
    }

    public function getLastEducationAttribute()
    {
        return $this->education_histories->first();
    }

    public function scopeIsLecturer($q)
    {
        return $q->where('lecturer_code', '!=', null)
                 ->where('lecturer_code', '!=', '');
    }

    public function getFullnameWithTitleAttribute()
    {
        return ($this->front_title ? $this->front_title.', ' : '').$this->fullname.($this->back_title ? ', '.$this->back_title : '');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'lecturer', 'emp_num');
    }

    public function lectures()
    {
        return $this->hasOne(Lectures::class, 'emp_num');
    }
}
