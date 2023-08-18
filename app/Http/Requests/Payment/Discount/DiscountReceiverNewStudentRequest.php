<?php

namespace App\Http\Requests\Payment\Discount;

use Illuminate\Foundation\Http\FormRequest;

class DiscountReceiverNewStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function attributes()
    {
        return [
            'md_id' => 'Potongan',
            'reg_id' => 'Mahasiswa',
            'mdr_period' => 'Periode',
            'mdr_nominal' => 'Nominal',
            'mdr_status' => 'Status',
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
            'md_id' => 'required',
            'reg_id' => 'required',
            'mdr_period' => 'required',
            'mdr_nominal' => 'required|numeric',
            'mdr_status' => 'nullable',
            'msc_id' => 'nullable',
        ];
    }
}
