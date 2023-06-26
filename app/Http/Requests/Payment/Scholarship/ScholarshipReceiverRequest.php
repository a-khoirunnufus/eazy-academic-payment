<?php

namespace App\Http\Requests\Payment\Scholarship;

use Illuminate\Foundation\Http\FormRequest;

class ScholarshipReceiverRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function attributes()
    {
        return [
            'ms_id' => 'Beasiswa',
            'student_number' => 'Mahasiswa',
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
            'ms_id' => 'required',
            'student_number' => 'required',
            'msr_period' => 'required',
            'msr_nominal' => 'required|numeric',
            'msr_status' => 'nullable',
            'msc_id' => 'nullable',
        ];
    }
}
