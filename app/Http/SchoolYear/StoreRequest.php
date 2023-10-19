<?php

namespace App\Http\Requests\SchoolYear;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\SchoolYear;
use App\Traits\Requests\PathParamGetter;
use App\Services\SchoolYearService;

class StoreRequest extends FormRequest
{
    use PathParamGetter;

    public function attributes()
    {
        return [
            'msy_start_date' => 'Tanggal Mulai',
            'msy_end_date' => 'Tanggal Akhir',
            'msy_code' => 'Kode Tahun Ajar'
        ];
    }

    public function rules()
    {   
        return array_merge([
            'msy_start_date' => 'required',
            'msy_end_date' => 'required|after:msy_start_date'
        ], $this->isMethod('post') ? [
            'msy_code' => 'required',
        ] : []);
    }

    protected function passedValidation()
    {
        if(!$this->isDateRangeValid()){
            abort(422, "Tanggal yang anda pilih sudah digunakan tahun ajar lainnya");
        }

        if(!$this->isMethod('post'))
            return;

        if($this->isSchoolYearCodeExists()){
            abort(422, "Tahun ajar sudah tersedia");
        }

        if(!$this->isSchoolYearCodeValid()){
            abort(422, "Format tahun ajar tidak valid");
        }
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated();

        if(!$this->isMethod('post'))
            return $validated;

        $yearSemester = SchoolYearService::fromCodeToYearAndSemester(
            $this->input('msy_code')
        );

        return array_merge($validated, [
            'school_year' => $yearSemester['year'],
            'semester' => $yearSemester['semester']
        ]);
    }

    private function isSchoolYearCodeExists()
    {
        return SchoolYear::where('msy_code', $this->input('msy_code'))
            ->count() > 0;
    }

    private function isSchoolYearCodeValid()
    {
        // must consist of four digit year and one digit semester (1 or 2)
        $pattern = '/^\d{4}[1-2]$/';
        
        return preg_match($pattern, $this->input('msy_code'));
    }

    private function isDateRangeValid()
    {
        $startDate = $this->input('msy_start_date');
        $endDate = $this->input('msy_end_date');

        $existQuery = SchoolYear::where(function($q) use($startDate, $endDate){
            $q->whereBetween('msy_start_date', [$startDate, $endDate])
            ->orWhereBetween('msy_end_date', [$startDate, $endDate]);
        });

        if(!$this->isMethod('post')){
            $existQuery->whereNot('msy_id', $this->getFirstParam());
        }

        return $existQuery->count() == 0;
    }
}
