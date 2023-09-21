<?php

namespace App\Http\Requests\Payment\Dispensation;

use Illuminate\Foundation\Http\FormRequest;

class DispensationSubmission extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function attributes()
    {
        return [
            'prr_dispensation_date' => 'Tanggal Dispensasi',
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
            'prr_dispensation_date' => 'required',
            'msc_id' => 'required',
            'url' => 'nullable',
        ];
    }
}
