<?php

namespace App\Imports;

use App\Models\Payment\Component as InvoiceComponent;
use App\Models\Payment\ComponentType as InvoiceComponentType;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use DB;

class InvoiceComponentsImport implements ToCollection, WithHeadingRow
{
    use Importable;

    private $import_id;
    private $validation_rules;
    private $attr_values;
    private $row = 9;

    public function __construct($import_id)
    {
        $this->import_id = $import_id;

        $this->validation_rules = [
            'nama_komponen' => [
                'required',
                'string',
                'min:1',
                function ($attribute, $value, $fail) {
                    if(InvoiceComponent::where('msc_name', '=', $value)->first()) {
                        $fail('Komponen dengan nama ini sudah ada pada sistem.');
                    }
                }
            ],
            'ditagihkan_kepada' => [
                'required',
                'string',
                'min:1',
                function ($attribute, $value, $fail) {
                    $temp = explode(',', $value);
                    foreach ($temp as $payer) {
                        $payer = strtolower(trim($payer));
                        if (!in_array($payer, ['pendaftar', 'mahasiswa lama', 'mahasiswa baru'])) {
                            $fail(ucfirst(str_replace('_', ' ', $attribute))." invalid.");
                            break;
                        }
                    }
                }
            ],
            'tipe_komponen' => [
                'required',
                'string',
                'min:1',
                function ($attribute, $value, $fail) {
                    $temp = explode(',', $value);
                    foreach ($temp as $type) {
                        $type = strtolower(trim($type));
                        $valid_type = false;

                        $invoice_component_types = $this->getInvoiceComponentType();
                        foreach ($invoice_component_types as $ms_type) {
                            if (strtolower($ms_type['msct_name']) == $type) {
                                $valid_type = true;
                                break;
                            }
                        }

                        if(!$valid_type) $fail(ucfirst(str_replace('_', ' ', $attribute))." invalid.");
                    }
                }
            ],
            'status_aktif' => [
                'required',
                'string',
                'min:1',
                function ($attribute, $value, $fail) {
                    $value = strtolower($value);
                    if (!in_array($value, ['aktif', 'tidak aktif'])) {
                        $fail(ucfirst(str_replace('_', ' ', $attribute))." invalid.");
                    }
                }
            ],
            'deskripsi' => 'nullable',
        ];

        $this->attr_values = [
            'nama_komponen' => 'Nama Komponen',
            'ditagihkan_kepada' => 'Ditagihkan Kepada',
            'tipe_komponen' => 'Tipe Komponen',
            'status_aktif' => 'Status Aktif',
            'deskripsi' => 'Deskripsi',
        ];
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $idx => $row)
        {
            $validator = $this->validateRow($row->toArray());

            $data = $this->preprocessingRow($row->toArray());

            $errors = [];
            if ($validator->fails()) {
                $data['status'] = 'invalid';
                $errors = $validator->errors()->toArray();
                $errors = Arr::flatten($errors);
            }
            if (count($errors) > 0) {
                $data['notes'] = implode(';', $errors);
            }

            DB::table('temp.finance_import_component')->insert($data);
        }
    }

    public function headingRow(): int
    {
        return $this->row;
    }

    /**
     * HELPERS
     */

    private function getInvoiceComponentType()
    {
        if (Cache::has('ms_invoice_component_types')) {
            return Cache::get('ms_invoice_component_types');
        } else {
            $value = InvoiceComponentType::all()->toArray();
            Cache::put('ms_invoice_component_types', $value, 30*60 );
            return $value;
        }
    }

    private function validateRow($row)
    {
        $this->row += 1;

        $msg_prefix = 'Pada baris '.$this->row.', ';
        $messages = [
            'required' => $msg_prefix.':attribute tidak boleh kosong.',
            'nama_komponen.exists' => $msg_prefix.':attribute dengan nilai :input sudah ada pada sistem.',
        ];

        return Validator::make($row, $this->validation_rules, $messages, $this->attr_values);
    }

    private function preprocessingRow($row)
    {
        $row_processed['import_id'] = $this->import_id;

        $row_processed['component_name'] = $row['nama_komponen'];

        $row_processed['is_participant'] = 0;
        $row_processed['is_new_student'] = 0;
        $row_processed['is_student'] = 0;
        $temp = explode(',', $row['ditagihkan_kepada']);
        foreach ($temp as $payer) {
            $payer = strtolower(trim($payer));
            if ($payer == 'pendaftar') {
                $row_processed['is_participant'] = 1;
            } elseif ($payer == 'mahasiswa baru') {
                $row_processed['is_new_student'] = 1;
            } elseif ($payer == 'mahasiswa lama') {
                $row_processed['is_student'] = 1;
            }
        }

        $row_processed['component_type'] = $row['tipe_komponen'];

        if (strtolower($row['status_aktif']) == 'aktif') {
            $row_processed['component_active_status'] = 1;
        } else {
            $row_processed['component_active_status'] = 0;
        }

        $row_processed['component_description'] = $row['deskripsi'];

        $row_processed['status'] = 'valid';

        $row_processed['notes'] = null;

        return $row_processed;
    }
}
