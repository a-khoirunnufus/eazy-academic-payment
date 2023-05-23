<?php

namespace App\Imports;

use App\Imports\Sheets\SettingFeeSheetImport;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use DB;

class SettingFeeImport implements WithMultipleSheets
{
    use Importable;

    private $num_sheets;

    public function __construct(int $num_sheets)
    {
        $this->num_sheets = $num_sheets;
    }

    public function sheets(): array
    {
        $sheets = [];

        if(!$this->validateSheets($this->num_sheets)) {
            // error
        }

        $import_id = DB::select("select nextval('temp.finance_import_setting_fee_import_id_num_seq')")[0]->nextval;

        for ($i=1; $i <= $this->num_sheets; $i+=2) {
            // component_fee sheet
            $sheets[] = new SettingFeeSheetImport($import_id, 13);

            // credit_schema sheet
            $sheets[] = new SettingFeeSheetImport($import_id, 14);
        }

        // dd($sheets);

        return $sheets;
    }

    /**
     * Cek jumlah sheet genap
     */
    private function validateSheets($num_sheets)
    {
        if ($num_sheets % 2 == 0) {
            return false;
        } else {
            return true;
        }
    }
}
