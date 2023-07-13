<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class CreditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    
     public function attributes()
     {
         return [
             'mcs_phone' => 'No Telepon',
             'mcs_email' => 'Email',
             'mcs_reason' => 'Alasan',
             'mcs_proof' => 'Bukti Pendukung',
             'mcs_method' => 'Metode Pembayaran',
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
            'mcs_school_year' => 'required',
            'mcs_phone' => 'required',
            'mcs_email' => 'required|email',
            'mcs_reason' => 'required',
            'mcs_method' => 'required',
            'mcs_proof' => 'required|file|mimes:jpg,png,pdf',
            'msc_id' => 'nullable',
        ];
    }
}
