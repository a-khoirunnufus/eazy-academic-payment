<?php

namespace App\Http\Requests\Payment\StudentBalanceWithdraw;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function attributes()
    {
        return [
            'student_id' => 'NIM Mahasiswa',
            'sbw_amount' => 'Nominal Penarikan',
            'sbw_related_files' => 'File Terkait'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'student_id' => ['required', Rule::exists('App\Models\Payment\Student', 'student_id')],
            'sbw_amount' => 'required|min:1',
            'sbw_related_files' => 'nullable',
        ];
    }
}
