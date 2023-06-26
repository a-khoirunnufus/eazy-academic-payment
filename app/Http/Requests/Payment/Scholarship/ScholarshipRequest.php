<?php

namespace App\Http\Requests\Payment\Scholarship;

use Illuminate\Foundation\Http\FormRequest;

class ScholarshipRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function attributes()
    {
        return [
            'ms_name' => 'Nama Potongan',
            'ms_type' => 'Tipe Beasiswa',
            'ms_from' => 'Nama Rekanan',
            'ms_from_name' => 'Nama Pemilik Rekanan',
            'ms_from_phone' => 'No Telp. Rekanan',
            'ms_from_email' => 'Email Rekanan',
            'ms_period_start' => 'Periode Awal',
            'ms_period_end' => 'Periode Akhir',
            'ms_nominal' => 'Nominal',
            'ms_budget' => 'Budget',
            'ms_status' => 'Status',
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
            'ms_name' => 'required',
            'ms_type' => 'required',
            'ms_from' => 'nullable',
            'ms_from_name' => 'nullable',
            'ms_from_phone' => 'nullable',
            'ms_from_email' => 'nullable|email',
            'ms_period_start' => 'required',
            'ms_period_end' => 'required',
            'ms_nominal' => 'required|numeric',
            'ms_budget' => 'required|numeric',
            'ms_status' => 'nullable',
            'msc_id' => 'nullable',
        ];
    }
}
