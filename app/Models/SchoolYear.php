<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use App\Traits\Models\Scopes\SchoolYear as SchoolYearScope;

class SchoolYear extends Model
{
    use SoftDeletes, SchoolYearScope;

    protected $table = 'masterdata.ms_school_year';
    protected $primaryKey = 'msy_id';
    protected $fillable = ['msy_year', 'msy_semester', 'msy_code', 'msy_start_date', 'msy_end_date'];
    protected $appends = ['semester_text'];


    public function getSemestersAttribute(){
        return SchoolYear::select('msy_id', 'msy_semester', 'msy_code')
                            ->where('msy_year', $this->msy_year)
                            ->get()
                            ->toArray();

    }

    public static function getMappedData(){
        $data = SchoolYear::select('msy_year')
                    ->groupBy('msy_year')
                    ->get();
        $result = [];
        foreach($data as $item){
            $result[] = [
                'msy_year' => $item->msy_year,
                'msy_semester' => $item->msy_semester
            ];
        }

        return $result;
    }

    public function final_task()
    {
        return $this->hasMany(FinalTask::class, 'ft_school_year', 'msy_code');
    }

    public function getSemesterTextAttribute()
    {
        return [
            'Ganjil',
            'Genap'
        ][intval($this->msy_semester) - 1];
    }

    public function scopeTestOne($query)
    {
        dd('test scope');
    }
}
