<?php

namespace App\Http\Controllers\_Payment\Api\Settings;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\Settings\PaymentRateRequest;
use App\Http\Requests\Payment\Settings\PaymentRateUpdateRequest;
use App\Models\LectureType;
use App\Models\MajorLectureType;
use App\Models\Period;
use App\Models\Path;
use App\Models\PeriodPath;
use App\Models\PeriodPathMajor;
use App\Models\Studyprogram;
use App\Models\Payment\Component;
use App\Models\Payment\ComponentDetail;
use App\Models\Payment\CreditSchema;
use App\Models\Payment\CreditSchemaDetail;
use App\Models\Payment\CreditSchemaPeriodPath;
use App\Models\Payment\CreditSchemaDeadline;
use App\Models\Scopes\CreditSchemaTemplateScope;
use App\Exports\SettingFeeTemplateExport;
use App\Imports\SettingFeeImport;
use DB;
use Builder;

class PaymentRatesController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->input('custom_filters');

        // remove item with null value or #ALL value
        $filters = array_filter($filters, function ($item) {
            return !is_null($item) && $item != '#ALL';
        });

        // $query = PaymentRate::query();
        // $query = $query->with('credit','path','period','studyProgram','component')->orderBy('f_id');

        $query = PeriodPath::with('major', 'period', 'path');

        if (isset($filters['school_year_id'])) {
            $query->whereHas('period.schoolyear', function ($q) use ($filters) {
                $q->where('msy_id', '=', $filters['school_year_id']);
            });
        }

        if (isset($filters['period_id'])) {
            $query->whereHas('period', function ($q) use ($filters) {
                $q->where('period_id', '=', $filters['period_id']);
            });
        }

        if (isset($filters['path_id'])) {
            $query->whereHas('path', function ($q) use ($filters) {
                $q->where('path_id', '=', $filters['path_id']);
            });
        }

        $data = $query->orderBy('ppd_id')->get();

        return datatables($data)->toJson();
    }

    public function detail($id)
    {
        $query = PeriodPathMajor::query();
        $query = $query->where('ppd_id', $id)->with([
                'majorLectureType',
                'credit',
                'credit.creditSchema' => function($query){
                    $query->withoutGlobalScope(CreditSchemaTemplateScope::class);
                },
                'periodPath',
            ])
            ->orderBy('ppm_id');
        $query = $query->get();
        $collection = collect();
        foreach ($query as $item) {
            $mma_id = 0;
            $mlt_id = 0;
            $path_id = 0;
            $period_id = 0;
            if ($item->majorLectureType) {
                $mma_id = $item->majorLectureType->mma_id;
                $mlt_id = $item->majorLectureType->mlt_id;
            }
            if ($item->periodPath) {
                $path_id = $item->periodPath->path_id;
                $period_id = $item->periodPath->period_id;
            }
            $search = ComponentDetail::with('component')->where('mma_id', $mma_id)->where('mlt_id', $mlt_id)->where('path_id', $path_id)->where('period_id', $period_id)->get();
            $data = ['ppm' => $item, 'component' => $search];
            $collection->push($data);
        }
        return datatables($collection)->toJson();
    }

    public function getComponent()
    {
        $component = Component::all();
        return $component->toJson();
    }

    public function getSchema()
    {
        $schema = CreditSchema::all();
        return $schema->toJson();
    }

    public function getSchemaById($ppm_id, $cs_id)
    {
        $schema = CreditSchemaPeriodPath::with(['creditSchema' => function($query){
                $query->withoutGlobalScope(CreditSchemaTemplateScope::class);
            }])
            ->where('ppm_id', $ppm_id)->where('cs_id', $cs_id)->first();
        if (!$schema) {
            $create = CreditSchemaPeriodPath::create([
                'cs_id' => $cs_id,
                'ppm_id' => $ppm_id
            ]);
            $schema = CreditSchemaPeriodPath::with(['creditSchema' => function($query){
                    $query->withoutGlobalScope(CreditSchemaTemplateScope::class);
                }])
                ->where('cspp_id', $create->cspp_id)->first();
        }
        return $schema->toJson();
    }

    public function removeSchemaById($ppm_id, $cs_id)
    {
        $schema = CreditSchemaPeriodPath::with(['creditSchema' => function($query){
                $query->withoutGlobalScope(CreditSchemaTemplateScope::class);
            }])
            ->where('ppm_id', $ppm_id)->where('cs_id', $cs_id)->delete();
        return json_encode(array('success' => true, 'message' => "Berhasil menghapus skema"));
    }

    public function update(PaymentRateUpdateRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            if (isset($validated['msc_id'])) {
                $count = count($validated['msc_id']);
                for ($i=0; $i < $count; $i++) {
                    if($validated['cd_id'][$i] == 0){
                        ComponentDetail::create([
                            'mma_id' => $validated['mma_id'][$i],
                            'msc_id' => $validated['msc_id'][$i],
                            'period_id' => $validated['period_id'][$i],
                            'path_id' => $validated['path_id'][$i],
                            'cd_fee' => $validated['cd_fee'][$i],
                            'msy_id' => $validated['msy_id'][$i],
                            'mlt_id' => $validated['mlt_id'][$i],
                            'ppm_id' => $validated['ppm_id'][$i]
                        ]);
                    } else {
                        $data = ComponentDetail::findorfail($validated['cd_id'][$i]);
                        $data->update([
                            'msc_id' => $validated['msc_id'][$i],
                            'cd_fee' => $validated['cd_fee'][$i]
                        ]);
                    }
                }
            }
            if (isset($validated['cs_id'])) {
                foreach ($validated['cs_id'] as $item) {
                    $data = CreditSchemaPeriodPath::where('cs_id', $item)->where('ppm_id', $validated['main_ppm_id'])->first();
                    if (!$data) {
                        CreditSchemaPeriodPath::create([
                            'cs_id' => $item,
                            'ppm_id' => $validated['main_ppm_id']
                        ]);
                    }
                }
                CreditSchemaPeriodPath::where('ppm_id', $validated['main_ppm_id'])->whereNotIn('cs_id', $validated['cs_id'])->delete();
            }
            if (isset($validated['cse_cs_id'])) {
                foreach ($validated['cse_cs_id'] as $key => $item) {
                    $data = CreditSchemaDeadline::where('cs_id', $item)->where('csd_id', $validated['cse_csd_id'][$key])->first();
                    if (!$data) {
                        CreditSchemaDeadline::create([
                            'cs_id' => $item,
                            'csd_id' => $validated['cse_csd_id'][$key],
                            'cse_deadline' => $validated['cse_deadline'][$key],
                        ]);
                    } else {
                        $data->update([
                            'cse_deadline' => $validated['cse_deadline'][$key]
                        ]);
                    }
                }
            }
            $text = "Berhasil memperbarui tarif dan pembayaran";
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function deleteComponent($id)
    {
        $data = ComponentDetail::findOrFail($id);
        $data->delete();

        return json_encode(array('success' => true, 'message' => "Berhasil menghapus komponen"));
    }

    /**
     * OLD CODE
     */
    // ppd_id => $id
    // public function import($id, Request $request)
    // {
    //     $file = $request->file('file');

    //     $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    //     // $reader->setReadDataOnly(true);
    //     $spreadsheet = $reader->load($file->getRealPath());

    //     $list_data = array();
    //     foreach ($spreadsheet->getSheetNames() as $list) {
    //         $sheet = $spreadsheet->getSheetByName($list);
    //         $dataSheet = $sheet->toArray();

    //         $data = array();
    //         $jenis_perkuliahan = array();
    //         $tagihan = array();
    //         $cicilan = array();
    //         for ($i = 2; $i < count($dataSheet); $i++) {
    //             if ($dataSheet[$i][0] != null && $dataSheet[$i][0] != NULL) {
    //                 array_push($jenis_perkuliahan, array(
    //                     "mlt_id" => $dataSheet[$i][0]
    //                 ));
    //             }
    //             if ($dataSheet[$i][1] != null && $dataSheet[$i][1] != NULL) {
    //                 $default_fee = $dataSheet[$i][2] == NULL ? 0 : $dataSheet[$i][2];
    //                 array_push($tagihan, array(
    //                     "msc_id" => $dataSheet[$i][1],
    //                     "fee" => $default_fee
    //                 ));
    //             }
    //             if ($dataSheet[$i][3] != null && $dataSheet[$i][3] != NULL) {
    //                 $default_date = $dataSheet[$i][4] == NULL ? date("Y-m-d") : $dataSheet[$i][4];
    //                 array_push($cicilan, array(
    //                     "cs_id" => $dataSheet[$i][3],
    //                     "tenggat" => array($default_date)
    //                 ));
    //             } else {
    //                 $default_date = $dataSheet[$i][4] == NULL ? date("Y-m-d") : $dataSheet[$i][4];
    //                 if (count($cicilan) > 0) {
    //                     array_push($cicilan[count($cicilan) - 1]["tenggat"], $default_date);
    //                 }
    //             }
    //         }
    //         array_push($data, array(
    //             "mma_id" => $spreadsheet->getSheetNames()[0],
    //             "detail" => array(
    //                 "jenis_perkuliahan" => $jenis_perkuliahan,
    //                 "komponen_tagihan" => $tagihan,
    //                 "cicilan" => $cicilan
    //             )
    //         ));

    //         array_push($list_data, $data);
    //     }

    //     //ambil data dari variabel $list_data untuk dimasukkan ke database

    //     //format response
    //     // return json_encode(array(
    //     //     'status' => 1, //1 untuk success/true, 0 untuk fail/false
    //     //     'message' => 'Berhasil import data' //message menyesuaikan kondisi
    //     // ));

    //     // return json_encode($list_data);
    // }
    // OLD CODE
    // public function store(request $request)
    // {
    //     $validated = $request->validated();
    //     // dd($validated);
    //     DB::beginTransaction();
    //     try{
    //         $paymentRate = PaymentRate::create([
    //             'f_period_id' => $validated['f_period_id'],
    //             'f_studyprogram_id' => $validated['f_studyprogram_id'],
    //             'f_path_id' => $validated['f_path_id'],
    //             'f_jenis_perkuliahan_id' => $validated['f_jenis_perkuliahan_id'],
    //         ]);
    //         $f_id = $paymentRate->f_id;

    //         $count = count($validated['cs_id']);
    //         for ($i=0; $i < $count; $i++) {
    //             PaymentCredit::create([
    //                 'f_id' => $f_id,
    //                 'cs_id' => $validated['cs_id'][$i]
    //             ]);
    //         }

    //         $count = count($validated['msc_id']);
    //         for ($i=0; $i < $count; $i++) {
    //             PaymentComponent::create([
    //                 'f_id' => $f_id,
    //                 'msc_id' => $validated['msc_id'][$i],
    //                 'fc_rate' => $validated['fc_rate'][$i],
    //             ]);
    //         }
    //         $text = "Berhasil menambahkan tarif dan pembayaran";
    //         DB::commit();
    //     }catch(\Exception $e){
    //         DB::rollback();
    //         return response()->json($e->getMessage());
    //     }
    //     return json_encode(array('success' => true, 'message' => $text));
    // }

    public function getPeriod()
    {
        $period = Period::all();
        return $period->toJson();
    }

    public function getPath()
    {
        $path = Path::all();
        return $path->toJson();
    }

    public function getStudyProgram()
    {
        $major = Studyprogram::all();
        return $major->toJson();
    }

    public function getLectureType()
    {
        $lecture = LectureType::all();
        return $lecture->toJson();
    }

    public function getCreditSchema()
    {
        $credit = CreditSchema::all();
        return $credit->toJson();
    }

    public function getRowData($id)
    {
        $query = PeriodPath::query();
        $query = $query->with('major', 'path', 'period')->where('ppd_id', '=', $id);
        // dd($query->get());
        return datatables($query)->toJson();
    }

    // public function delete($id)
    // {
    //     $data = PaymentRate::findOrFail($id);
    //     $data->delete();

    //     return json_encode(array('success' => true, 'message' => "Berhasil menghapus tarif dan pembayaran"));
    // }

    /**
     * IMPORT SETTING FEE START
     */

    public function downloadFileForImport(Request $request)
    {
        $validated = $request->validate([
            'period_path_id' => 'required|integer'
        ]);

        $period_path = PeriodPath::with(['period', 'path', 'major'])
            ->where(['ppd_id' => $validated['period_path_id']])
            ->first()
            ->toArray();

        // Period Data
        $period = $period_path['period'];

        // Path Data
        $path = $period_path['path'];

        // Academic Year Data
        $academic_year = $period_path['period']['schoolyear'];

        // Study Program Data
        $studyprogram_lecturetype_list = array_map(function($item) {
            return [
                'studyprogram_id' => $item['major_lecture_type']['study_program']['studyprogram_id'],
                'studyprogram_name' => $item['major_lecture_type']['study_program']['studyprogram_name'],
                'lecture_type_id' => $item['major_lecture_type']['lecture_type']['mlt_id'],
                'lecture_type_name' => $item['major_lecture_type']['lecture_type']['mlt_name'],
            ];
        }, $period_path['major']);

        $data = [
            'period' => $period,
            'path' => $path,
            'academic_year' => $academic_year,
            'studyprogram_lecturetype_list' => $studyprogram_lecturetype_list
        ];

        $export = new SettingFeeTemplateExport($data);

        return $export->download('Import Pengaturan Tarif_'.$period['period_name'].'_'.$path['path_name'].'_'.time().'.xlsx');
    }

    public function uploadFileForImport(Request $request)
    {
        $validated = $request->validate([
            'period_path_id' => 'required|integer',
            'file' => 'required|mimes:xlsx'
        ]);

        // define import_id
        $import_id = DB::select("select nextval('temp.finance_import_fee_import_id_num_seq')")[0]->nextval;

        $studyprogram_count = DB::table('masterdata.period_path_major')
            ->where('ppd_id', '=', $validated['period_path_id'])
            ->get()
            ->count();
        $num_sheets = $studyprogram_count * 2;

        try {
            $import = new SettingFeeImport($import_id, $num_sheets);
            $import->import($validated['file']);
        } catch (\Throwable $th) {
            Log::debug($th->getMessage());
            return response()->json([
                'success' => false,
                // 'message' => 'Terjadi Kesalahan!',
                'message' => $th->getMessage(),
            ], 500);
        }

        // send import id
        return response()->json([
            'success' => true,
            'message' => 'Selesai memproses file.',
            'payload' => [
                'import_id' => $import_id,
            ],
        ], 200);
    }

    public function dtImportPreview(Request $request)
    {
        $import_id = $request->input('custom_payload')['import_id'];

        $data = DB::table('temp.vw_finance_import_fee_master')
            ->where('import_id', '=', $import_id)
            ->get();

        return datatables($data)->toJson();
    }

    public function importSettingFee(Request $request)
    {
        $validated = $request->validate([
            'period_path_id' => 'required',
            'import_id' => 'required',
        ]);

        $period_path = PeriodPath::find($validated['period_path_id']);

        // CLEAR INVOICE COMPONENTS
        $ppm_ids = $this->clearComponents($validated['period_path_id']);

        // CLEAR CREDIT SCHEMAS
        $this->clearCreditSchemas($ppm_ids);

        // Get temp import data
        $master_records = DB::table('temp.finance_import_fee')
            ->where('import_id', '=', $validated['import_id'])
            ->get();

        foreach ($master_records as $master) {
            // Get major lecture type
            $major_lecture_type_id = MajorLectureType::where([
                ['mma_id', '=', $master->studyprogram_id],
                ['mlt_id', '=', $master->lecture_type_id]
            ])->first()->mma_lt_id;

            // Get period path major
            $period_path_major_id = PeriodPathMajor::where([
                ['ppd_id', '=', $period_path->ppd_id],
                ['mma_lt_id', '=', $major_lecture_type_id],
            ])->first()->ppm_id;

            try {
                DB::beginTransaction();

                // INSERT COMPONENT DETAILS
                $this->insertComponentDetails($master, $period_path_major_id);

                // INSERT CREDIT SCHEMAS
                $this->insertCreditSchemas($master, $period_path_major_id);

                DB::commit();
            } catch (\Throwable $th) {
                DB::rollback();
                Log::debug('Skip record for period:'.$master->period_id.', path:'.$master->path_id.', studyprogram:'.$master->studyprogram_id.', lecture_type:'.$master->lecture_type_id);
            }

        }

        // CLEAR TEMPORARY IMPORT DATA
        $this->clearTempImports($validated['import_id']);

        // return response()->json([
        //     'success' => false,
        //     'message' => $e->getMessage(),
        // ], 500);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil import setting tarif dan pembayaran.'
        ], 200);
    }

    /**
     * SECTION importSettingFee() HELPER BEGIN
     */

    private function clearComponents($period_path_id)
    {
        $ppm_ids = DB::table('temp.finance_import_fee as fif')
            ->leftJoin('masterdata.ms_major_lecture_type as mmlt', function($join) {
                $join->on('mmlt.mma_id', '=', 'fif.studyprogram_id');
                $join->on('mmlt.mlt_id', '=', 'fif.lecture_type_id');
            })
            ->leftJoin('masterdata.period_path_major as ppm', 'ppm.mma_lt_id', '=', 'mmlt.mma_lt_id')
            ->select('ppm.ppm_id')
            ->where('ppm.ppd_id', '=', $period_path_id)
            ->groupBy('ppm.ppm_id', 'mmlt.mma_lt_id')
            ->get()
            ->toArray();

        $ppm_ids = array_map(function($item) {
            return $item->ppm_id;
        }, $ppm_ids);

        foreach ($ppm_ids as $ppm_id) {
            ComponentDetail::where('ppm_id', '=', $ppm_id)->delete();
        }

        return $ppm_ids;
    }

    private function clearCreditSchemas($ppm_ids)
    {
        $cs_ids = DB::table('finance.credit_schema_periodpath')
            ->select('cs_id')
            ->whereIn('ppm_id', $ppm_ids)
            ->whereNull('deleted_at')
            ->groupBy('cs_id')
            ->get()
            ->toArray();

        $cs_ids = array_map(function($item) {
            return $item->cs_id;
        }, $cs_ids);

        // clear credit schema
        foreach ($cs_ids as $cs_id) {
            $credit_schema = CreditSchema::withTrashed()->withoutGlobalScope(CreditSchemaTemplateScope::class)->where('cs_id', '=', $cs_id)->first();
            if ($credit_schema->is_template == false) {
                CreditSchemaPeriodPath::withTrashed()->withoutGlobalScope(CreditSchemaTemplateScope::class)->where('cs_id', '=', $cs_id)->forceDelete();
                CreditSchemaDeadline::withTrashed()->withoutGlobalScope(CreditSchemaTemplateScope::class)->where('cs_id', '=', $cs_id)->forceDelete();
                CreditSchemaDetail::withTrashed()->withoutGlobalScope(CreditSchemaTemplateScope::class)->where('csd_cs_id', '=', $cs_id)->forceDelete();
                CreditSchema::withTrashed()->withoutGlobalScope(CreditSchemaTemplateScope::class)->where('cs_id', '=', $cs_id)->forceDelete();
            } else {
                CreditSchemaPeriodPath::where('cs_id', '=', $cs_id)->delete();
                CreditSchemaDeadline::where('cs_id', '=', $cs_id)->delete();
            }
        }
    }

    private function insertComponentDetails($master, $period_path_major_id)
    {
        // column id on temp.finance_import_fee
        $import_fee_id = $master->id;

        // temp component (imported)
        $temp_components = DB::table('temp.finance_import_fee_component_detail')
            ->where('import_fee_id', '=', $import_fee_id)
            ->get();

        // get school year
        $school_year_id = Period::find($master->period_id)->msy_id;

        foreach ($temp_components as $temp_component) {

            if ($temp_component->status == 'invalid') {
                throw new Exception("Data Invalid, Skipping.");
            }

            // get component id by component name
            $ms_component = DB::table('finance.ms_component')->where('msc_name', '=', $temp_component->component_name)->first();

            // insert new record of component_detail
            ComponentDetail::create([
                'mma_id' => $master->studyprogram_id, // studyprogram_id
                'msc_id' => $ms_component->msc_id, // component_id
                'period_id' => $master->period_id,
                'path_id' => $master->path_id,
                'cd_fee' => $temp_component->component_amount,
                'msy_id' => $school_year_id, // school_year
                'mlt_id' => $master->lecture_type_id, // lecture_type
                'ppm_id' => $period_path_major_id,
            ]);
        }
    }

    private function insertCreditSchemas($master, $period_path_major_id)
    {
        $import_fee_id = $master->id;

        $temp_credit_schema_types = DB::table('temp.finance_import_fee_credit_schema_detail')
            ->where('import_fee_id', '=', $import_fee_id)
            ->groupBy('credit_schema_name')
            ->select('credit_schema_name')
            ->get();

        foreach ($temp_credit_schema_types as $temp_credit_schema_type) {
            // get credit_schema
            $ms_credit_schema = CreditSchema::where('cs_name', '=', $temp_credit_schema_type->credit_schema_name)->first();

            // CASE WHEN CREDIT_SCHEMA NOT EXIST
            if ($ms_credit_schema == null) {
                // create new credit_schema(not for template)
                $imp_credit_schema = CreditSchema::create([
                    'cs_name' => 'IMP_'.$period_path_major_id.': '.$temp_credit_schema_type->credit_schema_name,
                    'cs_valid' => 'yes',
                    'is_template' => false,
                ]);

                $temp_credit_schema_details = DB::table('temp.finance_import_fee_credit_schema_detail')
                    ->where('import_fee_id', '=', $import_fee_id)
                    ->where('credit_schema_name', '=', $temp_credit_schema_type->credit_schema_name)
                    ->get();

                foreach ($temp_credit_schema_details as $temp_credit_schema_detail) {

                    if ($temp_credit_schema_detail->status == 'invalid') {
                        throw new Exception("Data Invalid, Skipping.");
                    }

                    // create credit_schema_detail, (installment percentage)
                    CreditSchemaDetail::create([
                        'csd_cs_id' => $imp_credit_schema->cs_id,
                        'csd_order' => $temp_credit_schema_detail->item_order,
                        'csd_percentage' => $temp_credit_schema_detail->percentage,
                    ]);
                }

                $ms_credit_schema = $imp_credit_schema;
            }

            // insert new record of credit_schema_period_path
            CreditSchemaPeriodPath::create([
                'cs_id' => $ms_credit_schema->cs_id,
                'ppm_id' => $period_path_major_id,
            ]);

            // get temp credit_schema_detail
            $temp_credit_schema_details = DB::table('temp.finance_import_fee_credit_schema_detail')
                ->where('import_fee_id', '=', $import_fee_id)
                ->where('credit_schema_name', '=', $temp_credit_schema_type->credit_schema_name)
                ->get();

            foreach ($temp_credit_schema_details as $temp_credit_schema_detail) {
                $ms_credit_schema_detail = CreditSchemaDetail::where('csd_cs_id', '=', $ms_credit_schema->cs_id)
                    ->where('csd_order', '=', $temp_credit_schema_detail->item_order)
                    ->first();

                // create credit_schema_deadline, (installment due date)
                $csd_arr = explode('-', $temp_credit_schema_detail->due_date);
                $credit_schema_deadline = $csd_arr[2].'-'.$csd_arr[1].'-'.$csd_arr[0];
                CreditSchemaDeadline::create([
                    'cs_id' => $ms_credit_schema->cs_id,
                    'csd_id' => $ms_credit_schema_detail->csd_id,
                    'cse_deadline' => $credit_schema_deadline,
                ]);
            }
        }
    }

    private function clearTempImports($import_id)
    {
        // Clear temp import data
        $temp_imports = DB::table('temp.finance_import_fee')
            ->where('import_id', '=', $import_id)
            ->get();

        foreach ($temp_imports as $temp_import) {
            DB::table('temp.finance_import_fee_component_detail')
                ->where('import_fee_id', '=', $temp_import->id)
                ->delete();

            DB::table('temp.finance_import_fee_credit_schema_detail')
                ->where('import_fee_id', '=', $temp_import->id)
                ->delete();
        }

        DB::table('temp.finance_import_fee')
            ->where('import_id', '=', $import_id)
            ->delete();
    }

    /**
     * SECTION importSettingFee() HELPER END
     */
}
