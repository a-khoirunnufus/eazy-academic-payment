<?php

namespace App\Http\Requests\Payment\Settings;

use Illuminate\Foundation\Http\FormRequest;

class CourseRateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return false;
    // }

    public function attributes()
    {
        return [
            'mcr_course_id' => 'Mata Kuliah',
            'mcr_tingkat' => 'Tingkat',
            'mcr_rate' => 'Tarif',
            'mcr_active_status' => 'Aktif Status',
            'mcr_is_package' => 'Paket'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'mcr_id' => 'nullable',
            'mcr_course_id' => 'required',
            'mcr_tingkat' => 'required',
            'mcr_rate' => 'required',
            'mcr_active_status' => 'nullable',
            'mcr_is_package' => 'required'
        ];
    }
}
