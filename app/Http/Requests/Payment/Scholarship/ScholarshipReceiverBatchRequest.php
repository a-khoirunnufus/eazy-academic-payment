<?php

namespace App\Http\Requests\Payment\Scholarship;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Payment\Student;
use App\Models\Payment\Scholarship;
use App\Models\Payment\Period;

class ScholarshipReceiverBatchRequest extends FormRequest
{
    private $receiver_count;

    public function __construct()
    {
        $this->$receiver_count = count($this->get('student_number') ?? []);
    }
    /**
     * Determine if the user is authorized to make this request.
     */
    public function attributes()
    {
        return [
            'student_number' => 'NIM Mahasiswa',
            'ms_id' => 'Beasiswa',
            'msr_period' => 'Periode',
            'msr_nominal' => 'Nominal',
            'msr_status' => 'Status',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'student_number' => 'required|array',
            'student_number.*' => Rule::exists(Student::class, 'student_number'),

            'ms_id' => 'required|array|size:'.$this->receiver_count,
            'ms_id.*' => Rule::exists(Scholarship::class, 'ms_id'),

            'msr_period' => 'required|array|size:'.$this->receiver_count,
            'msr_period.*' => Rule::exists(Period::class, 'period_id'),

            'msr_nominal' => 'required|array|size:'.$this->receiver_count,
            'msr_nominal.*' => 'numeric',

            'msr_status' => 'required|array|size:'.$this->receiver_count,
            'msr_status.*' => 'in:0,1',
        ];
    }
}
