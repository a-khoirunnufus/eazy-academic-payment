<?php

namespace App\Imports\Sheets;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Illuminate\Support\Facades\Log;
use DB;

class SettingFeeComponentSheetImport implements ToCollection, WithHeadingRow, WithEvents
{
    use RegistersEventListeners;

    private $sheet_title;
    private $import_id;
    private $sheet_type;
    // private $period_id;
    // private $path_id;
    // private $studyprogram_id;
    // private $lecture_type_id;
    private $import_fee_id;

    // validation related
    private $row = 13;

    public function __construct($import_id)
    {
        $this->import_id = $import_id;
    }

    public static function beforeSheet(BeforeSheet $event)
    {
        $sheet = $event->getDelegate();
        $encrypted_string = $sheet->getCell('A8');
        $encrypted_string = str_replace("Jangan ubah kode berikut: ", "", $encrypted_string);
        $payload = json_decode($encrypted_string, true);

        $self = $event->getConcernable();
        $self->setSheetTitle($sheet->getTitle());
        $self->setSheetType($payload['sheet_type']);
        // $self->setPeriodId($payload['period_id']);
        // $self->setPathId($payload['path_id']);
        // $self->setStudyprogramId($payload['studyprogram_id']);
        // $self->setLectureTypeId($payload['lecture_type_id']);

        // check is main record already exist
        $import_id = $self->getImportId();
        $import_fee = DB::table('temp.finance_import_fee')
            ->where('import_id', '=', $import_id)
            ->where('period_id', '=', $payload['period_id'])
            ->where('path_id', '=', $payload['path_id'])
            ->where('studyprogram_id', '=', $payload['studyprogram_id'])
            ->where('lecture_type_id', '=', $payload['lecture_type_id'])
            ->orderBy('id', 'desc')
            ->first();

        // if dont exist create one
        if (!$import_fee) {
            // TODO: validasi berdasarkan kombinasi period_id, path_id, studyprogram_id, lecture_type_id
            $data = [
                'import_id' => $import_id,
                'period_id' => $payload['period_id'],
                'path_id' => $payload['path_id'],
                'studyprogram_id' => $payload['studyprogram_id'],
                'lecture_type_id' => $payload['lecture_type_id'],
            ];

            $rules = [
                'period_id' => 'required|exists:pgsql.masterdata.ms_period,period_id',
                'path_id' => 'required|exists:pgsql.masterdata.ms_path,path_id',
                'studyprogram_id' => 'required|exists:pgsql.masterdata.ms_studyprogram,studyprogram_id',
                'lecture_type_id' => 'required|exists:pgsql.masterdata.ms_lecture_type,mlt_id',
            ];

            $msg_prefix = 'Pada sheet '.$sheet->getTitle().', ';
            $messages = [
                'required' => $msg_prefix.':attribute tidak boleh kosong.',
                'period_id.exists' => $msg_prefix.'Periode invalid.',
                'path_id.exists' => $msg_prefix.'Jalur invalid.',
                'studyprogram_id.exists' => $msg_prefix.'Program Studi invalid.',
                'lecture_type_id.exists' => $msg_prefix.'Jenis Perkuliahan invalid.',
            ];

            $validator = Validator::make($data, $rules, $messages);

            $import_fee_id = null;
            if (!$validator->fails()) {
                $import_fee_id = DB::table('temp.finance_import_fee')->insertGetId($data);
            }

            $self->setImportFeeId($import_fee_id);
        }
    }

    public function collection(Collection $rows)
    {
        $rules = [
            'component_name' => 'required|string|exists:pgsql.finance.ms_component,msc_name',
            'component_amount' => 'required|integer',
        ];

        foreach ($rows as $idx => $row)
        {
            $this->row += 1;

            $data = [
                'import_fee_id' => $this->import_fee_id,
                'component_name' => $row['nama_komponen_tagihan'],
                'component_amount' => $row['nominal_tagihan'],
                'status' => 'valid',
            ];

            $msg_prefix = 'Pada sheet '.$this->sheet_title.', pada baris '.$this->row.', ';
            $messages = [
                'required' => $msg_prefix.':attribute tidak boleh kosong.',
                'component_name.exists' => $msg_prefix.':attribute dengan nilai :input tidak tersedia pada sistem.',
            ];

            $validator = Validator::make($data, $rules, $messages);

            $errors = [];
            if ($validator->fails()) {
                $data['status'] = 'invalid';
                $errors = $validator->errors()->toArray();
                $errors = Arr::flatten($errors);
            }

            if (count($errors) > 0) {
                $data['notes'] = implode(';', $errors);
            }

            DB::table('temp.finance_import_fee_component_detail')->insert($data);
        }
    }

    public function headingRow(): int
    {
        return $this->row;
    }

    /**
     * GETTERS
     */

    public function getImportId()
    {
        return $this->import_id;
    }

    /**
     * SETTERS
     */

    public function setSheetTitle($sheet_title)
    {
        $this->sheet_title = $sheet_title;
    }

    public function setSheetType($sheet_type)
    {
        $this->sheet_type = $sheet_type;
    }

    // public function setPeriodId($period_id)
    // {
    //     $this->period_id = $period_id;
    // }

    // public function setPathId($path_id)
    // {
    //     $this->path_id = $path_id;
    // }

    // public function setStudyprogramId($studyprogram_id)
    // {
    //     $this->studyprogram_id = $studyprogram_id;
    // }

    // public function setLectureTypeId($lecture_type_id)
    // {
    //     $this->lecture_type_id = $lecture_type_id;
    // }

    public function setImportFeeId($import_fee_id)
    {
        $this->import_fee_id = $import_fee_id;
    }
}
