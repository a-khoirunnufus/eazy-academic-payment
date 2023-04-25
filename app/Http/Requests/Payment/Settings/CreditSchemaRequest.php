<?php

namespace App\Http\Requests\Payment\Settings;

use Illuminate\Foundation\Http\FormRequest;

class CreditSchemaRequest extends FormRequest
{
    public function attributes()
    {
        return [
            'cs_name' => 'Nama Skema Cicilan',
            'cs_valid' => 'Status Validitas',
            'csd_percentage' => 'Persentase Pembayaran',
            'csd_date' => 'Tenggat Pembayaran'
        ];
    }

    public function rules(): array
    {
        return [
            'cs_name' => 'required',
            'cs_valid' => 'required',
            'csd_percentage' => 'required',
            'csd_date' => 'required'
        ];
    }
}
