<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\SettingFeeTemplateSheetExport;
use App\Models\Payment\Component as InvoiceComponent;
use App\Models\Payment\CreditSchema;

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

        // Invoice Component Data
        $invoice_components = InvoiceComponent::where([
                ['active_status', '=', 1],
                ['msc_is_new_student', '=', 1],
                ['msct_id', '=', 1],
            ])
            ->get()
            ->toArray();
        $invoice_component_data = array_map(function($item) {
            return [
                'NAMA_KOMPONEN_TAGIHAN' => $item['msc_name'],
                'NOMINAL_TAGIHAN' => 0
            ];
        }, $invoice_components);

        // Credit Schema Data
        $credit_schemas = CreditSchema::with('creditSchemaDetail')
            ->where('cs_valid', '=', 'yes')
            ->get()
            ->toArray();
        usort($credit_schemas, function($a, $b) {
            return count($a['credit_schema_detail']) <=> count($b['credit_schema_detail']);
        });
        $credit_schema_data = [];
        foreach ($credit_schemas as $credit_schema) {
            foreach ($credit_schema['credit_schema_detail'] as $idx => $schema_detail) {
                $temp = [
                    'SKEMA_CICILAN' => $credit_schema['cs_name'],
                    'CICILAN_KE' => $idx+1,
                    'PERSENTASE_PEMBAYARAN' => $schema_detail['csd_percentage'],
                    'TENGGAT_PEMBAYARAN' => null,
                ];
                $credit_schema_data[] = $temp;
            }
        }

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
                $invoice_component_data,
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
                $credit_schema_data,
            );
        }

        return $sheets;
    }
}
