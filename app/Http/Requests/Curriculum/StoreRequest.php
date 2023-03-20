<?php

namespace App\Http\Requests\Curriculum;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function attributes()
    {
        return [
            'name' => 'Nama Kurikulum',
            'applied_date' => 'Tanggal Mulai Berlaku',
            'book_document' => 'Dokumen Buku Kurikulum',
            'sk_document' => 'Dokumen SK',
            'report_document' => 'Dokumen Laporan',
            'ba_document' => 'Dokumen BA'
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
            'name' => 'required',
            'studyprogram_id' => 'required',
            'applied_date' => 'required|date',
            'book_document' => ['nullable', Rule::exists('App\Models\Resource', 'id')],
            'sk_document' => ['nullable', Rule::exists('App\Models\Resource', 'id')],
            'report_document' => ['nullable', Rule::exists('App\Models\Resource', 'id')],
            'ba_document' => ['nullable', Rule::exists('App\Models\Resource', 'id')]
        ];
    }
}
