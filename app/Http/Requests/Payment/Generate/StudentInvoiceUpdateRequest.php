<?php

namespace App\Http\Requests\Payment\Generate;

use Illuminate\Foundation\Http\FormRequest;

class StudentInvoiceUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'prr_id' => 'required',
            'prrd_id' => 'nullable',
            'prrd_component' => 'nullable',
            'prrd_amount' => 'nullable',
            'title' => 'nullable',
            'url' => 'nullable',
        ];
    }
}
