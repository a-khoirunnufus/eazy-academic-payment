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
            'csd_percentage.*' => 'Persentase Pembayaran',
            'cs_status' => 'Status',
        ];
    }

    public function rules(): array
    {
        return [
            'cs_name' => 'required|min:1',
            'csd_percentage' => [
                'required',
                'array',
                'min:1',
                function ($attribute, $value, $fail) {
                    $sum = array_sum($value);
                    if ($sum < 100) {
                        $fail("Jumlah persentase tidak boleh kurang dari 100.");
                    } elseif ($sum > 100) {
                        $fail("Jumlah persentase tidak boleh lebih dari 100.");
                    }
                }
            ],
            'csd_percentage.*' => 'required|numeric|min:0.000000001|max:100',
            'cs_valid' => 'required|min:1',
            'cs_status' => 'required|min:1',
        ];
    }
}
