<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class DispensationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function attributes()
     {
         return [
             'mds_phone' => 'No Telepon',
             'mds_email' => 'Email',
             'mds_reason' => 'Alasan',
             'mds_proof' => 'Bukti Pendukung',
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
            'student_number' => 'required',
            'mds_school_year' => 'required',
            'mds_phone' => 'required',
            'mds_email' => 'required|email',
            'mds_reason' => 'required',
            'mds_proof' => 'required|file|mimes:jpg,png,pdf',
            'msc_id' => 'nullable',
        ];
    }
}
