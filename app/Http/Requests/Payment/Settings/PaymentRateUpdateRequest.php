<?php

namespace App\Http\Requests\Payment\Settings;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRateUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            
            'main_ppm_id' => 'required',
            'cd_id' => 'nullable',
            'mma_id' => 'nullable',
            'period_id' => 'nullable',
            'path_id' => 'nullable',
            'msy_id' => 'nullable',
            'mlt_id' => 'nullable',
            'ppm_id' => 'nullable',
            'msc_id' => 'nullable',
            'cd_fee' => 'nullable',
            'cs_id' => 'nullable',
            'cse_deadline' => 'nullable',
            'cse_cs_id' => 'nullable',
            'cse_csd_id' => 'nullable',
        ];
    }
}
