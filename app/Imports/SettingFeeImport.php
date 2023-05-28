<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;
use App\Imports\Sheets\SettingFeeSheetImport;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use DB;

class SettingFeeImport implements WithMultipleSheets
{
    use Importable;

    private $import_id;
    private $num_sheets;
    private $component_fee_header_row = 13;
    private $credit_schema_header_row = 14;

    public function __construct(int $import_id, int $num_sheets)
    {
        $this->import_id = $import_id;
        $this->num_sheets = $num_sheets;
    }

    public function sheets(): array
    {
        $sheets = [];

        for ($i=1; $i <= $this->num_sheets; $i+=2) {
            // component_fee sheet
            $sheets[] = new SettingFeeSheetImport($this->import_id, $this->component_fee_header_row);

            // credit_schema sheet
            $sheets[] = new SettingFeeSheetImport($this->import_id, $this->credit_schema_header_row);
        }

        return $sheets;
    }
}
