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
        $import_id = DB::select("select nextval('temp.finance_import_setting_fee_import_id_num_seq')")[0]->nextval;

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

        $pre_query = DB::table('temp.finance_import_setting_fee as tfisf')
            ->leftJoin('masterdata.ms_studyprogram as mms', 'tfisf.studyprogram_id', '=', 'mms.studyprogram_id')
            ->leftJoin('masterdata.ms_lecture_type as mslt', 'tfisf.lecture_type_id', '=', 'mslt.mlt_id')
            ->select(
                'mms.studyprogram_name',
                'mslt.mlt_name',
                DB::raw("
                    CASE
                        WHEN tfisf.setting_fee_type = 'component_fee' THEN
                            string_agg('{' ||
                                '\"name\":\"' || tfisf.column_1 || '\",' ||
                                '\"nominal\":\"' || tfisf.column_2 || '\"' ||
                            '}', ',')
                        ELSE NULL
                    END as invoice_component
                "),
                DB::raw("
                    CASE
                        WHEN tfisf.setting_fee_type = 'credit_schema' THEN
                            string_agg('{' ||
                                '\"percentage\":\"' || tfisf.column_1 || '\",' ||
                                '\"due_date\":\"' || tfisf.column_2 || '\"' ||
                            '}', ',')
                        ELSE NULL
                    END as installment
                "),
                'tfisf.status',
                DB::raw("string_agg(tfisf.notes, ';') as notes"),
            )
            ->where('tfisf.import_id', '=', $import_id)
            ->groupBy('mms.studyprogram_name', 'mslt.mlt_name', 'tfisf.setting_fee_type', 'tfisf.status', 'tfisf.order')
            ->orderBy('mms.studyprogram_name', 'asc')
            ->orderBy('mslt.mlt_name', 'asc')
            ->orderBy('tfisf.order', 'asc');

        $data = DB::table('pre')
            ->withExpression('pre', $pre_query)
            ->select(
                'pre.studyprogram_name as studyprogram',
                'pre.mlt_name as lecture_type',
                DB::raw("'[' || string_agg(pre.invoice_component, ',') || ']' as invoice_components"),
                DB::raw("'[' || string_agg(pre.installment, ',') || ']' as installments"),
                DB::raw("string_agg(pre.status, ';') as statuses"),
                DB::raw("string_agg(pre.notes, ';') as notes"),
            )
            ->groupBy('pre.studyprogram_name', 'pre.mlt_name')
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

        DB::beginTransaction();

        try {

            // get temp import data
            $data = DB::table('temp.finance_import_setting_fee')
                ->where('import_id', '=', $validated['import_id'])
                ->where('status', 'not like', '%invalid%')
                ->get();

            $ppm_ids = DB::table('temp.finance_import_setting_fee as fisf')
                ->leftJoin('masterdata.ms_major_lecture_type as mmalt', function($join) {
                    $join->on('mmalt.mma_id', '=', 'fisf.studyprogram_id');
                    $join->on('mmalt.mlt_id', '=', 'fisf.lecture_type_id');
                })
                ->leftJoin('masterdata.period_path_major as ppm', 'ppm.mma_lt_id', '=', 'mmalt.mma_lt_id')
                ->select('ppm.ppm_id')
                ->where('ppm.ppd_id', '=', $validated['period_path_id'])
                ->groupBy('ppm.ppm_id', 'mmalt.mma_lt_id')
                ->get()
                ->toArray();
            $ppm_ids = array_map(function($item) {
                return $item->ppm_id;
            }, $ppm_ids);

            $cs_ids = DB::table('finance.credit_schema_periodpath')
                ->select('cs_id')
                ->whereIn('ppm_id', $ppm_ids)
                ->groupBy('cs_id')
                ->get()
                ->toArray();
            $cs_ids = array_map(function($item) {
                return $item->cs_id;
            }, $cs_ids);

            // clear component detail
            foreach ($ppm_ids as $ppm_id) {
                ComponentDetail::where('ppm_id', '=', $ppm_id)->forceDelete();
            }

            // clear credit schema
            foreach ($cs_ids as $cs_id) {
                $credit_schema = CreditSchema::withTrashed()->withoutGlobalScope(CreditSchemaTemplateScope::class)->where('cs_id', '=', $cs_id)->first();
                if ($credit_schema->is_template == false) {
                    CreditSchemaPeriodPath::withTrashed()->withoutGlobalScope(CreditSchemaTemplateScope::class)->where('cs_id', '=', $cs_id)->forceDelete();
                    CreditSchemaDeadline::withTrashed()->withoutGlobalScope(CreditSchemaTemplateScope::class)->where('cs_id', '=', $cs_id)->forceDelete();
                    CreditSchemaDetail::withTrashed()->withoutGlobalScope(CreditSchemaTemplateScope::class)->where('csd_cs_id', '=', $cs_id)->forceDelete();
                    CreditSchema::withTrashed()->withoutGlobalScope(CreditSchemaTemplateScope::class)->where('cs_id', '=', $cs_id)->forceDelete();
                } else {
                    CreditSchemaPeriodPath::withTrashed()->where('cs_id', '=', $cs_id)->forceDelete();
                }
            }

            $cs_prev = 0;
            $credit_schema_order = 0;
            foreach ($data as $item) {
                // get major lecture type
                $major_lecture_type_id = MajorLectureType::where([
                    ['mma_id', '=', $item->studyprogram_id],
                    ['mlt_id', '=', $item->lecture_type_id]
                ])->first()->mma_lt_id;

                // get period path major
                $period_path_major_id = PeriodPathMajor::where([
                    ['ppd_id', '=', $period_path->ppd_id],
                    ['mma_lt_id', '=', $major_lecture_type_id],
                ])->first()->ppm_id;

                if ($item->setting_fee_type == 'component_fee') {
                    // get component id by component name
                    $ms_component = DB::table('finance.ms_component')->where('msc_name', '=', $item->column_1)->first();

                    // get school year
                    $school_year_id = Period::find($item->period_id)->msy_id;

                    // insert new record of component_detail
                    ComponentDetail::create([
                        'mma_id' => $item->studyprogram_id, // studyprogram_id
                        'msc_id' => $ms_component->msc_id, // component_id
                        'period_id' => $item->period_id,
                        'path_id' => $item->path_id,
                        'cd_fee' => $item->column_2,
                        'msy_id' => $school_year_id, // school_year
                        'mlt_id' => $item->lecture_type_id, // lecture_type
                        'ppm_id' => $period_path_major_id,
                    ]);
                }
                elseif ($item->setting_fee_type == 'credit_schema') {
                    if ($item->studyprogram_id != $cs_prev) {
                        // create new credit_schema(not for template)
                        $credit_schema = CreditSchema::create([
                            'cs_name' => 'IMP_'.$period_path_major_id,
                            'cs_valid' => 'yes',
                            'is_template' => false,
                        ]);

                        // insert new record of credit_schema_period_path
                        CreditSchemaPeriodPath::create([
                            'cs_id' => $credit_schema->cs_id,
                            'ppm_id' => $period_path_major_id,
                        ]);
                    } else {
                        $credit_schema = CreditSchema::withoutGlobalScope(CreditSchemaTemplateScope::class)
                            ->where('cs_name', '=', 'IMP_'.$period_path_major_id)
                            ->first();
                    }

                    // create credit_schema_detail, (installment percentage)
                    $credit_schema_detail = CreditSchemaDetail::create([
                        'csd_cs_id' => $credit_schema->cs_id,
                        'csd_order' => $credit_schema_order+1,
                        'csd_percentage' => $item->column_1,
                    ]);

                    // create credit_schema_deadline, (installment due date)
                    $csd_arr = explode('-', $item->column_2);
                    $credit_schema_deadline = $csd_arr[2].'-'.$csd_arr[1].'-'.$csd_arr[0];
                    CreditSchemaDeadline::create([
                        'cs_id' => $credit_schema->cs_id,
                        'csd_id' => $credit_schema_detail->csd_id,
                        'cse_deadline' => $credit_schema_deadline,
                    ]);

                    if ($item->studyprogram_id != $cs_prev) {
                        $cs_prev = $item->studyprogram_id;
                    }
                }

            }

            // clear temp import data
            $data = DB::table('temp.finance_import_setting_fee')
                ->where('import_id', '=', $validated['import_id'])
                ->delete();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil import setting tarif dan pembayaran.'
        ], 200);
    }
}
