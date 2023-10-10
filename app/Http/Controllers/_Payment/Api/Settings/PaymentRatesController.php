<?php

namespace App\Http\Controllers\_Payment\Api\Settings;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\Settings\PaymentRateRequest;
use App\Http\Requests\Payment\Settings\PaymentRateUpdateRequest;
use App\Models\Payment\LectureType;
use App\Models\Payment\MajorLectureType;
use App\Models\Payment\Period;
use App\Models\Payment\Path;
use App\Models\Payment\PeriodPath;
use App\Models\Payment\PeriodPathMajor;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\Component;
use App\Models\Payment\ComponentDetail;
use App\Models\Payment\CreditSchema;
use App\Models\Payment\CreditSchemaDetail;
use App\Models\Payment\CreditSchemaPeriodPath;
use App\Models\Payment\CreditSchemaDeadline;
use App\Models\Scopes\CreditSchemaTemplateScope;
use App\Exports\SettingFeeTemplateExport;
use App\Imports\SettingFeeImport;
use App\Traits\Payment\LogActivity;
use App\Traits\Payment\General;
use App\Enums\Payment\LogStatus;
use DB;
use Builder;

class PaymentRatesController extends Controller
{
    use LogActivity, General;

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
            $search = ComponentDetail::with('component')->where('mma_id', $mma_id)->where('mlt_id', $mlt_id)->where('path_id', $path_id)->where('period_id', $period_id)->where('cd_is_admission',0)->get();
            $searchNew = ComponentDetail::with('component')->where('mma_id', $mma_id)->where('mlt_id', $mlt_id)->where('path_id', $path_id)->where('period_id', $period_id)->where('cd_is_admission',1)->get();
            $creditNew = CreditSchemaPeriodPath::with('creditSchema')->where('ppm_id', $item->ppm_id)->where('cs_is_admission',1)->get();
            $data = ['ppm' => $item, 'credit' => $item->credit->where('cs_is_admission',0)->toArray(), 'creditNew' => $creditNew, 'component' => $search, 'componentNew' => $searchNew, 'ppm_id' => $item->ppm_id, 'ppd_id' => $id];
            $collection->push($data);
        }
        return datatables($collection)->toJson();
    }

    public function getComponent($is_admission)
    {
        $component = Component::query();
        if($is_admission == 1){
            $component = $component->where('msc_is_new_student',1);
        }else{
            $component = $component->where('msc_is_student',1);
        }
        return $component->get()->toJson();
    }

    public function getSchema()
    {
        $schema = CreditSchema::all();
        return $schema->toJson();
    }

    public function getDetailSchemaById($cs_id)
    {
        $schema = CreditSchemaDetail::with('creditSchemaDeadline','creditSchema')->where('csd_cs_id',$cs_id)->get();
        return $schema->toJson();
    }

    public function getSchemaById($ppm_id, $cs_id,$is_admission)
    {
        $schema = CreditSchemaPeriodPath::with(['creditSchema' => function($query){
                $query->withoutGlobalScope(CreditSchemaTemplateScope::class);
            }])
            ->where('ppm_id', $ppm_id)->where('cs_id', $cs_id)->where('cs_is_admission', $is_admission)->first();

        if (!$schema) {
            $create = CreditSchemaPeriodPath::create([
                'cs_id' => $cs_id,
                'ppm_id' => $ppm_id,
                'cs_is_admission' => $is_admission,
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
        $log = $this->addToLog('Update Tarif dan Pembayaran',$this->getAuthId(),LogStatus::Process,$request->url);
        $result = $this->updateProcess($request,$log->log_id);
        $this->updateLogStatus($log,$result);
        return $result;
    }

    public function updateProcess(PaymentRateUpdateRequest $request,$log_id){
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
                            'ppm_id' => $validated['main_ppm_id'],
                            'cd_is_admission' => $validated['is_admission'],
                        ]);
                        $component = Component::findorfail($validated['msc_id'][$i]);
                        $this->addToLogDetail($log_id,$this->getLogTitle('Add '.$component->msc_name.' - Rp.'.$validated['cd_fee'][$i].' at '.$validated['title']),LogStatus::Success);
                    } else {
                        $data = ComponentDetail::findorfail($validated['cd_id'][$i]);
                        $data->update([
                            'msc_id' => $validated['msc_id'][$i],
                            'cd_fee' => $validated['cd_fee'][$i]
                        ]);
                        $component = Component::findorfail($validated['msc_id'][$i]);
                        $this->addToLogDetail($log_id,$this->getLogTitle('Update '.$component->msc_name.' - Rp.'.$validated['cd_fee'][$i].' at '.$validated['title']),LogStatus::Success);
                    }
                }
            }
            if (isset($validated['cs_id'])) {
                foreach ($validated['cs_id'] as $item) {
                    $data = CreditSchemaPeriodPath::where('cs_id', $item)->where('ppm_id', $validated['main_ppm_id'])->where('cs_is_admission',$validated['is_admission'])->first();
                    $creditSchema = CreditSchema::findorfail($item);
                    if (!$data) {
                        CreditSchemaPeriodPath::create([
                            'cs_id' => $item,
                            'ppm_id' => $validated['main_ppm_id'],
                            'cs_is_admission' => $validated['is_admission'],
                        ]);
                        $this->addToLogDetail($log_id,$this->getLogTitle('Add Credit Schema '.$creditSchema->cs_name.' at '.$validated['title']),LogStatus::Success);
                    }
                    $this->addToLogDetail($log_id,$this->getLogTitle('Update Credit Schema '.$creditSchema->cs_name.' at '.$validated['title']),LogStatus::Success);
                }
                CreditSchemaPeriodPath::where('ppm_id', $validated['main_ppm_id'])->where('cs_is_admission',$validated['is_admission'])->whereNotIn('cs_id', $validated['cs_id'])->delete();
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
            $this->addToLogDetail($log_id,$this->getLogTitle($validated['title'],$e->getMessage()),LogStatus::Failed);
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

    public function delete(Request $request, $ppm_id)
    {
        $log = $this->addToLog('Hapus Tarif dan Pembayaran',$this->getAuthId(),LogStatus::Process,$request->url);
        $result = $this->deleteProcess($ppm_id,$log->log_id,$request->is_admission);
        $this->updateLogStatus($log,$result);
        return $result;
    }

    public function deleteBulk(Request $request, $ppd_id)
    {
        $log = $this->addToLog('Delete Bulk Tarif dan Pembayaran',$this->getAuthId(),LogStatus::Process,$request->url);
        $ppm = PeriodPathMajor::where('ppd_id', $ppd_id)->get();
        foreach ($ppm as $item) {
            $this->deleteProcess($item->ppm_id,$log->log_id,null);
        }
        $this->updateLogStatus($log,LogStatus::Success);
        $text = "Berhasil Delete Bulk Tarif dan Pembayaran";
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function deleteProcess($ppm_id,$log_id,$is_admission){
        $ppm = PeriodPathMajor::with('majorLectureType')->findorfail($ppm_id);
        try {
            DB::beginTransaction();
            if($is_admission == null){
                ComponentDetail::where('ppm_id',$ppm_id)->delete();
                CreditSchemaPeriodPath::where('ppm_id',$ppm_id)->delete();
            }else{
                ComponentDetail::where('ppm_id',$ppm_id)->where('cd_is_admission',$is_admission)->delete();
                CreditSchemaPeriodPath::where('ppm_id',$ppm_id)->where('cs_is_admission',$is_admission)->delete();
            }

            $this->addToLogDetail($log_id,$this->getLogTitle($ppm->majorLectureType->studyProgram->studyprogram_name.' - '.$ppm->majorLectureType->lectureType->mlt_name),LogStatus::Success);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            $this->addToLogDetail($log_id,$this->getLogTitle($ppm->majorLectureType->studyProgram->studyprogram_name.' - '.$ppm->majorLectureType->lectureType->mlt_name,$e->getMessage()),LogStatus::Failed);
            return response()->json($e->getMessage());
        }
        $text = "Berhasil menghapus tarif dan pembayaran";
        return json_encode(array('success' => true, 'message' => $text));
    }

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

        $filename_raw = 'Import Pengaturan Tarif_'.$period['period_name'].'_'.$path['path_name'].'_'.time();
        $filename_sanitazed = preg_replace('/[^a-zA-Z0-9_-]+/', '_', strtolower($filename_raw)).'.xlsx';

        return $export->download($filename_sanitazed);
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
