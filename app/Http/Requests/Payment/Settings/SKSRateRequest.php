<?php

namespace App\Http\Requests\Payment\Settings;

use Illuminate\Foundation\Http\FormRequest;

class SKSRateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function attributes()
    {
        return [
            'msr_studyprogram_id' => 'Program Studi',
            'mcr_tingkat' => 'Tingkat',
            'mcr_rate' => 'Tarif Normal',
            'msr_rate_practicum' => 'Tarif Praktikum',
            'mcr_active_status' => 'Aktif Status'
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
            'id' => 'nullable',
            'msr_studyprogram_id' => 'required',
            'msr_tingkat' => 'required',
            'msr_rate' => 'required',
            'msr_rate_practicum' => 'required',
            'msr_active_status' => 'nullable',
        ];
    }
}
