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

class SettingFeeSheetImport implements ToCollection, WithHeadingRow, WithEvents
{
    use RegistersEventListeners;

    private $sheet_title;
    private $import_id;
    private $sheet_type;
    private $period_id;
    private $path_id;
    private $studyprogram_id;
    private $lecture_type_id;
    private $header_row;

    // validation related
    private $row;

    public function __construct($import_id, $header_row)
    {
        $this->import_id = $import_id;
        $this->header_row = $header_row;
        $this->row = $header_row;
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
        $self->setPeriodId($payload['period_id']);
        $self->setPathId($payload['path_id']);
        $self->setStudyprogramId($payload['studyprogram_id']);
        $self->setLectureTypeId($payload['lecture_type_id']);
    }

    public function collection(Collection $rows)
    {
        // TODO: validasi berdasarkan kombinasi period_id, path_id, studyprogram_id, lecture_type_id
        $rules = [
            'setting_fee_type' => 'required|in:component_fee,credit_schema',
            'period_id' => 'required|exists:pgsql.masterdata.ms_period,period_id',
            'path_id' => 'required|exists:pgsql.masterdata.ms_path,path_id',
            'studyprogram_id' => 'required|exists:pgsql.masterdata.ms_studyprogram,studyprogram_id',
            'lecture_type_id' => 'required|exists:pgsql.masterdata.ms_lecture_type,mlt_id'
        ];

        if ($this->sheet_type == 'component_fee') {
            $rules['column_1'] = 'required|string|exists:pgsql.finance.ms_component,msc_name';
            $rules['column_2'] = 'required|integer';
        } elseif ($this->sheet_type == 'credit_schema') {
            $rules['column_1'] = 'required|integer|min:0|max:100';
            $rules['column_2'] = 'required|date_format:d-m-Y';
        }

        // check credit_schema percentage
        $sum_percentage = 0;
        // credit_schema deadline validation
        $previous_time = null;
        $order = 0;
        foreach ($rows as $idx => $row)
        {
            $this->row += 1;
            $order++;

            $data = [
                'import_id' => $this->import_id,
                'setting_fee_type' => $this->sheet_type,
                'period_id' => $this->period_id,
                'path_id' => $this->path_id,
                'studyprogram_id' => $this->studyprogram_id,
                'lecture_type_id' => $this->lecture_type_id,
                'status' => 'valid',
                'order' => $order,
            ];

            if ($this->sheet_type == 'component_fee') {
                $data['column_1'] = $row['nama_komponen_tagihan'];
                $data['column_2'] = $row['nominal_tagihan'];
            } elseif ($this->sheet_type == 'credit_schema') {
                $data['column_1'] = $row['persentase_pembayaran'];
                $data['column_2'] = $row['tenggat_pembayaran'];
            }

            $msg_prefix = 'Pada sheet '.$this->sheet_title.', pada baris '.$this->row.', ';
            $messages = [
                'required' => $msg_prefix.':attribute tidak boleh kosong.',
                'period_id.exists' => $msg_prefix.'Periode invalid.',
                'path_id.exists' => $msg_prefix.'Jalur invalid.',
                'studyprogram_id.exists' => $msg_prefix.'Program Studi invalid.',
                'lecture_type_id.exists' => $msg_prefix.'Jenis Perkuliahan invalid.',
                'setting_fee_type.in' => $msg_prefix.':attribute harus bernilai component_fee atau credit_schema',
                'column_1.exists' => $msg_prefix.':attribute dengan nilai :input tidak tersedia pada sistem.',
                'column_1.min' => $msg_prefix.':attribute tidak boleh kurang dari 0.',
                'column_1.max' => $msg_prefix.':attribute tidak boleh lebih dari 100.',
            ];

            $attr_values = [];
            if ($this->sheet_type == 'component_fee') {
                $attr_values = [
                    'column_1' => 'Nama Komponen Tagihan',
                    'column_2' => 'Nominal Tagihan',
                ];
            } elseif ($this->sheet_type == 'credit_schema') {
                $attr_values = [
                    'column_1' => 'Persentase Pembayaran',
                    'column_2' => 'Tenggat Pembayaran',
                ];
            }

            $validator = Validator::make($data, $rules, $messages, $attr_values);

            $errors = [];
            if ($validator->fails()) {
                $data['status'] = 'invalid';
                $errors = $validator->errors()->toArray();
                $errors = Arr::flatten($errors);
            }

            if ($this->sheet_type == 'credit_schema') {
                // validasi persentase
                $sum_percentage += intval($data['column_1']);
                if ($idx+1 == count($rows)) {
                    if ($sum_percentage < 100) {
                        $data['status'] = 'invalid';
                        $errors[] = 'Pada sheet '.$this->sheet_title.', Jumlah akumulasi persentase tidak boleh kurang dari 100.';
                    }
                } else {
                    if ($sum_percentage > 100) {
                        $data['status'] = 'invalid';
                        $errors[] = 'Pada sheet '.$this->sheet_title.', Jumlah akumulasi persentase tidak boleh lebih dari 100.';
                    }
                }

                // validasi tenggat pembayaran
                $date_arr = explode('-', $data['column_2']);
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
            }

            if (count($errors) > 0) {
                $data['notes'] = implode(';', $errors);
            }

            DB::table('temp.finance_import_setting_fee')->insert($data);
        }
    }

    public function headingRow(): int
    {
        return $this->header_row;
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
