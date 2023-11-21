<?php

namespace App\Http\Requests\Payment\Discount;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Payment\Student;
use App\Models\Payment\Discount;
use App\Models\Payment\Year;

class DiscountReceiverBatchRequest extends FormRequest
{
    private $receiver_count;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function attributes()
    {
        return [
            'student_number' => 'NIM Mahasiswa',
            'md_id' => 'Potongan',
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
            'student_number' => 'required|array',
            'student_number.*' => Rule::exists(Student::class, 'student_number'),

            'md_id' => 'required|array|size:'.$this->receiver_count,
            'md_id.*' => Rule::exists(Discount::class, 'md_id'),

            'mdr_period' => 'required|array|size:'.$this->receiver_count,
            'mdr_period.*' => Rule::exists(Year::class, 'msy_id'),

            'mdr_nominal' => 'required|array|size:'.$this->receiver_count,
            'mdr_nominal.*' => 'numeric',

            'mdr_status' => 'required|array|size:'.$this->receiver_count,
            'mdr_status.*' => 'in:0,1',
        ];
    }

    public function prepareForValidation()
    {
        $this->receiver_count = count($this->get('student_number') ?? []);
    }
}
