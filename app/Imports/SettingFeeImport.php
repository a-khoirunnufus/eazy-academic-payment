<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;
use App\Imports\Sheets\SettingFeeComponentSheetImport;
use App\Imports\Sheets\SettingFeeInstallmentSheetImport;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use DB;

class SettingFeeImport implements WithMultipleSheets
{
    use Importable;

    private $import_id;
    private $num_sheets;

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
            $sheets[] = new SettingFeeComponentSheetImport($this->import_id);

            // credit_schema sheet
            $sheets[] = new SettingFeeInstallmentSheetImport($this->import_id);
        }

        return $sheets;
    }
}
