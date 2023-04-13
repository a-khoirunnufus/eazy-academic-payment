<?php

namespace App\Http\Requests\Payment\Settings;

use Illuminate\Foundation\Http\FormRequest;

class ComponentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return false;
    // }

    public function attributes()
    {
        return [
            'msc_name' => 'Kode Komponen Tagihan',
            'msc_description' => 'Nama Komponen Tagihan',
            'msct_id' => 'Jenis Komponen Tagihan',
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
            'msc_name' => 'required',
            'msc_description' => 'required',
            'msct_id' => 'required',
            'msc_is_student' => 'nullable',
            'msc_is_new_student' => 'nullable',
            'msc_is_participant' => 'nullable',
            'msc_id' => 'nullable',
        ];
    }
}
