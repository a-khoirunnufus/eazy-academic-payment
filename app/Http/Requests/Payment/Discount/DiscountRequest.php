<?php

namespace App\Http\Requests\Payment\Discount;

use Illuminate\Foundation\Http\FormRequest;

class DiscountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    
    public function attributes()
    {
        return [
            'md_name' => 'Nama Potongan',
            'md_period_start' => 'Periode Awal',
            'md_period_end' => 'Periode Akhir',
            'md_nominal' => 'Nominal',
            'md_budget' => 'Budget',
            'md_status' => 'Status',
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
            'md_name' => 'required',
            'md_period_start' => 'required',
            'md_period_end' => 'required',
            'md_nominal' => 'required|numeric',
            'md_budget' => 'required|numeric',
            'md_status' => 'nullable',
            'msc_id' => 'nullable',
        ];
    }
}
