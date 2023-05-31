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

class SettingFeeInstallmentSheetImport implements ToCollection, WithHeadingRow, WithEvents
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
    private $row = 14;

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

        $self->setImportFeeId($import_fee->id);
    }

    public function collection(Collection $rows)
    {
        $rules = [
            'credit_schema_name' => 'required|exists:pgsql.finance.credit_schema,cs_name',
            'item_order' => 'required|integer',
            'percentage' => 'required|integer|min:0|max:100',
            'due_date' => 'required|date_format:d-m-Y',
        ];

        $current_schema = null;
        // check credit_schema percentage
        $sum_percentage = 0;
        // credit_schema deadline validation
        $previous_time = null;

        foreach ($rows as $idx => $row)
        {
            $this->row += 1;

            if ($current_schema != $row['skema_cicilan']) {
                $current_schema = $row['skema_cicilan'];
                $sum_percentage = 0;
                $previous_time = null;
            }

            $data = [
                'import_fee_id' => $this->import_fee_id,
                'credit_schema_name' => $row['skema_cicilan'],
                'item_order' => $row['cicilan_ke'],
                'percentage' => $row['persentase_pembayaran'],
                'due_date' => $row['tenggat_pembayaran'],
                'status' => 'valid',
            ];

            $msg_prefix = 'Pada sheet '.$this->sheet_title.', pada baris '.$this->row.', ';
            $messages = [
                'required' => $msg_prefix.':attribute tidak boleh kosong.',
                'credit_schema_name.exists' => $msg_prefix.':attribute dengan nilai :input tidak tersedia pada sistem.',
                'percentage.min' => $msg_prefix.':attribute tidak boleh kurang dari 0.',
                'percentage.max' => $msg_prefix.':attribute tidak boleh lebih dari 100.',
            ];

            $validator = Validator::make($data, $rules, $messages);

            $errors = [];
            if ($validator->fails()) {
                $data['status'] = 'invalid';
                $errors = $validator->errors()->toArray();
                $errors = Arr::flatten($errors);
            }

            // validasi persentase
            $sum_percentage += intval($row['persentase_pembayaran']);
            if ($current_schema == $row['skema_cicilan']) {
                if ($sum_percentage > 100) {
                    $data['status'] = 'invalid';
                    $errors[] = 'Pada sheet '.$this->sheet_title.', Jumlah akumulasi persentase pada setiap skema tidak boleh lebih dari 100.';
                }
            }
            if (isset($rows[$idx+1])) {
                if ($current_schema != $rows[$idx+1]['skema_cicilan']) {
                    if ($sum_percentage < 100) {
                        $data['status'] = 'invalid';
                        $errors[] = 'Pada sheet '.$this->sheet_title.', Jumlah akumulasi persentase pada setiap skema tidak boleh kurang dari 100.';
                    }
                }
            } else {
                if ($sum_percentage < 100) {
                    $data['status'] = 'invalid';
                    $errors[] = 'Pada sheet '.$this->sheet_title.', Jumlah akumulasi persentase pada setiap skema tidak boleh kurang dari 100.';
                }
            }

            // validasi tenggat pembayaran
            $date_arr = explode('-', $row['tenggat_pembayaran']);
            $date_iso_string = $date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0];
            $current_time = strtotime($date_iso_string);
            if ($previous_time) {
                if ($previous_time > $current_time) {
                    $data['status'] = 'invalid';
                    $errors[] = 'Pada sheet '.$this->sheet_title.', Tanggal harus berurut dari tanggal paling dekat ke tanggal paling lama.';
                }
            }
            if ($current_time <= time()) {
                $data['status'] = 'invalid';
                $errors[] = 'Pada sheet '.$this->sheet_title.', Tanggal tidak boleh sama dengan atau kurang dari hari ini.';
            }
            $previous_time = $current_time;

            if (count($errors) > 0) {
                $data['notes'] = implode(';', $errors);
            }

            DB::table('temp.finance_import_fee_credit_schema_detail')->insert($data);
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
