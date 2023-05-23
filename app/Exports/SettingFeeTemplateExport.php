<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\SettingFeeTemplateSheetExport;

class SettingFeeTemplateExport implements WithMultipleSheets
{
    use Exportable;

    private $sheets_data;

    public function __construct(array $sheets_data)
    {
        $this->sheets_data = $sheets_data;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->sheets_data['studyprogram_lecturetype_list'] as $item) {
            $identities = [
                'sheet_type' => 'component_fee',
                'period_id' => $this->sheets_data['period']['period_id'],
                'path_id' => $this->sheets_data['path']['path_id'],
                'studyprogram_id' => $item['studyprogram_id'],
                'lecture_type_id' => $item['lecture_type_id'],
                // 'excel_header_row' => 14,
            ];

            $sheets[] = new SettingFeeTemplateSheetExport(
                'component_fee',
                $item['studyprogram_name'].' - '.$item['lecture_type_name'].' (1)',
                $this->sheets_data['academic_year']['msy_year'],
                $this->sheets_data['period']['period_name'],
                $this->sheets_data['path']['path_name'],
                $item['studyprogram_name'],
                $item['lecture_type_name'],
                [
                    'Nominal tagihan diisi dengan nilai nominal tanpa tanda koma(,).',
                ],
                json_encode($identities),
                [[
                    'NAMA_KOMPONEN_TAGIHAN' => 'Contoh Komponen Tagihan',
                    'NOMINAL_TAGIHAN' => 100000
                ]]
            );

            $identities['sheet_type'] = 'credit_schema';
            // $identities['excel_header_row'] = 15;
            $sheets[] = new SettingFeeTemplateSheetExport(
                'credit_schema',
                $item['studyprogram_name'].' - '.$item['lecture_type_name'].' (2)',
                $this->sheets_data['academic_year']['msy_year'],
                $this->sheets_data['period']['period_name'],
                $this->sheets_data['path']['path_name'],
                $item['studyprogram_name'],
                $item['lecture_type_name'],
                [
                    'Persentase pembayaran diisi dengan nilai persentase tanpa tanda %.',
                    'Tenggat pembayaran diisi dengan tanggal dengan format DD-MM-YYYY.',
                ],
                json_encode($identities),
                [[
                    'PERSENTASE_PEMBAYARAN' => '100',
                    'TENGGAT_PEMBAYARAN' => '01-12-2023'
                ]]
            );
        }

        return $sheets;
    }
}
