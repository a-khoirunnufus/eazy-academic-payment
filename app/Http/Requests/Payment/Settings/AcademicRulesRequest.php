<?php

namespace App\Http\Requests\Payment\Settings;

use Illuminate\Foundation\Http\FormRequest;

class AcademicRulesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return false;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'periode' => 'required',
            'aturan' => 'required',
            'komponen' => 'required',
            'cicilan' => 'required',
            'minimum_paid' => 'required',
            'is_active' => 'required'
        ];
    }
}
