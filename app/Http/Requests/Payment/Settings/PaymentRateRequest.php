<?php

namespace App\Http\Requests\Payment\Settings;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function attributes()
    {
        return [
            'f_period_id' => 'Periode',
            'f_studyprogram_id' => 'Study Program',
            'f_path_id' => 'Jalur',
            'f_jenis_perkuliahan_id' => 'Jenis Perkuliahan',
            'cs_id' => 'Skema Cicilan',
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
            'f_period_id' => 'required',
            'f_studyprogram_id' => 'required',
            'f_path_id' => 'required',
            'f_jenis_perkuliahan_id' => 'required',
            'cs_id' => 'required',
            'fc_id' => 'nullable',
            'msc_id' => 'nullable',
            'fc_rate' => 'nullable'
        ];
    }
}
