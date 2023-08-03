<?php

namespace App\Http\Requests\Payment\Credit;

use Illuminate\Foundation\Http\FormRequest;

class CreditSubmission extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function attributes()
    {
        return [
            'cse_amount' => 'Nominal',
            'cse_deadline' => 'Deadline',
            'cse_order' => 'Order',
            'cs_id' => 'Skema Kredit',
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
            'cse_amount' => 'required',
            'cse_deadline' => 'required',
            'cse_order' => 'required',
            'msc_id' => 'required',
            'cs_id' => 'required',
        ];
    }
}
