<?php

namespace App\Imports;

use App\Models\Payment\Component as InvoiceComponent;
use App\Models\Payment\ComponentType as InvoiceComponentType;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class InvoiceComponentsImport
implements
    ToModel,
    SkipsEmptyRows,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure
{
    use Importable, SkipsFailures;

    public $imported_rows = 0;

    /**
     * Kolom 0: NAMA_KOMPONEN
     * Kolom 1: DITAGIHKAN_KEPADA
     * Kolom 2: TIPE_KOMPONEN
     * Kolom 3: STATUS_AKTIF
     * Kolom 4: DESKRIPSI
     */

    public function model(array $row)
    {
        $row_processed = $this->preprocessingRow($row);

        $this->imported_rows++;

        return new InvoiceComponent([
            'msc_name' => $row_processed['msc_name'],
            'msc_is_participant' => $row_processed['msc_is_participant'],
            'msc_is_new_student' => $row_processed['msc_is_new_student'],
            'msc_is_student' => $row_processed['msc_is_student'],
            'msct_id' => $row_processed['msct_id'],
            'active_status' => $row_processed['active_status'],
            'msc_description' => $row_processed['msc_description'],
        ]);
    }

    public function headingRow(): int
    {
        return 9;
    }

    public function rules(): array
    {
        return [
            'nama_komponen' => 'required|string|min:1|unique:App\Models\Payment\Component,msc_name',
            'ditagihkan_kepada' => [
                'required',
                'string',
                'min:1',
                function ($attribute, $value, $fail) {
                    $temp = explode(',', $value);
                    foreach ($temp as $payer) {
                        $payer = strtolower(trim($payer));
                        if (!in_array($payer, ['pendaftar', 'mahasiswa lama', 'mahasiswa baru'])) {
                            $fail($attribute." invalid.");
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

                        if(!$valid_type) $fail($attribute." invalid.");
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
                        $fail($attribute." invalid.");
                    }
                }
            ],
            'deskripsi' => 'nullable',
        ];
    }

    public function customValidationAttributes()
    {
        return [
            'nama_komponen' => 'Nama Komponen',
            'ditagihkan_kepada' => 'Ditagihkan Kepada',
            'tipe_komponen' => 'Tipe Komponen',
            'status_aktif' => 'Status Aktif',
            'deskripsi' => 'Deskripsi',
        ];
    }

    private function preprocessingRow($row)
    {
        $row_processed['msc_name'] = $row['nama_komponen'];

        $row_processed['msc_is_participant'] = 0;
        $row_processed['msc_is_new_student'] = 0;
        $row_processed['msc_is_student'] = 0;
        $temp = explode(',', $row['ditagihkan_kepada']);
        foreach ($temp as $payer) {
            $payer = strtolower(trim($payer));
            if ($payer == 'pendaftar') {
                $row_processed['msc_is_participant'] = 1;
            } elseif ($payer == 'mahasiswa baru') {
                $row_processed['msc_is_new_student'] = 1;
            } elseif ($payer == 'mahasiswa lama') {
                $row_processed['msc_is_student'] = 1;
            }
        }

        $invoice_component_types = $this->getInvoiceComponentType();
        foreach ($invoice_component_types as $type) {
            if (strtolower($type['msct_name']) == strtolower($row['tipe_komponen'])) {
                $row_processed['msct_id'] = intval($type['msct_id']);
                break;
            }
        }

        if (strtolower($row['status_aktif']) == 'tidak aktif') {
            $row_processed['active_status'] = 0;
        } elseif (strtolower($row['status_aktif']) == 'aktif') {
            $row_processed['active_status'] = 0;
        }

        $row_processed['msc_description'] = $row['deskripsi'];

        return $row_processed;
    }

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
}
