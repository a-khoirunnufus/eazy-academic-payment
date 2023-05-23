<?php

namespace App\Imports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Illuminate\Support\Facades\Log;
use DB;

class SettingFeeSheetImport implements ToCollection, WithHeadingRow, WithEvents
{
    use RegistersEventListeners;

    private $import_id;
    private $sheet_type;
    private $period_id;
    private $path_id;
    private $studyprogram_id;
    private $lecture_type_id;
    private $header_row;

    public function __construct($import_id, $header_row)
    {
        $this->import_id = $import_id;
        $this->header_row = $header_row;
    }

    public static function beforeSheet(BeforeSheet $event)
    {
        $encrypted_string = $event->getDelegate()->getCell('A8');
        $encrypted_string = str_replace("Jangan ubah kode berikut: ", "", $encrypted_string);
        $payload = json_decode($encrypted_string, true);

        $self = $event->getConcernable();
        $self->setSheetType($payload['sheet_type']);
        $self->setPeriodId($payload['period_id']);
        $self->setPathId($payload['path_id']);
        $self->setStudyprogramId($payload['studyprogram_id']);
        $self->setLectureTypeId($payload['lecture_type_id']);
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            $data = [
                'import_id' => $this->import_id,
                'setting_fee_type' => $this->sheet_type,
                'period_id' => $this->period_id,
                'path_id' => $this->path_id,
                'studyprogram_id' => $this->studyprogram_id,
                'lecture_type_id' => $this->lecture_type_id,
            ];

            if ($this->sheet_type == 'component_fee') {
                $data['column_1'] = $row['nama_komponen_tagihan'];
                $data['column_2'] = $row['nominal_tagihan'];
            } elseif ($this->sheet_type == 'credit_schema') {
                $data['column_1'] = $row['persentase_pembayaran'];
                $data['column_2'] = $row['tenggat_pembayaran'];
            }

            DB::table('temp.finance_import_setting_fee')->insert($data);
        }
    }

    public function headingRow(): int
    {
        return $this->header_row;
    }

    public function setSheetType($sheet_type)
    {
        $this->sheet_type = $sheet_type;
    }

    public function setPeriodId($period_id)
    {
        $this->period_id = $period_id;
    }

    public function setPathId($path_id)
    {
        $this->path_id = $path_id;
    }

    public function setStudyprogramId($studyprogram_id)
    {
        $this->studyprogram_id = $studyprogram_id;
    }

    public function setLectureTypeId($lecture_type_id)
    {
        $this->lecture_type_id = $lecture_type_id;
    }
}
