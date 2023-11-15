<?php

namespace App\Services\Payment;

use App\Models\Payment\Student;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentDetail;
use App\Models\Payment\LeaveReceiver;
use App\Traits\Payment\LogActivity;
use App\Traits\Payment\General;
use App\Enums\Payment\LogStatus;
use Carbon\Carbon;
use DB;

class LeaveInvoice {
    use LogActivity, General;

    private $is_admission = 0;
    private $type_id = 7; #finance.ms_component_type

    public function storeLeaveGenerate($studentNumber)
    {
        $student = Student::with('getComponent')->findorfail($studentNumber);
        $components = $student->getComponent()
            ->where('path_id', $student->path_id)
            ->where('period_id', $student->period_id)
            ->where('msy_id', $student->msy_id)
            ->where('mlt_id', $student->mlt_id)
            ->where('mma_id', $student->studyprogram_id)
            ->where('type_id', $this->type_id)
            ->where('cd_is_admission', $this->is_admission)
            ->get();
        $prr_total = 0;
        if (!$components->isEmpty()) {
            foreach ($components as $item) {
                $prr_total = $prr_total + $item->cd_fee;
            }
        }
        $default = $this->getCacheSetting('payment_leave_default_cache');
        if($default){
            $prr_total = $prr_total+ (int)$default;
        }
        DB::beginTransaction();
        try {
            $status = 'belum lunas';
            $bool_status = 0;
            if($prr_total == 0){
                $status = 'lunas';
                $bool_status = 1;
            }

            $payment = Payment::create([
                'prr_status' => $status,
                'prr_total' => $prr_total,
                'prr_paid_net' => $prr_total,
                'student_number' => $student->student_number,
                'prr_type' => $this->type_id,
                'prr_school_year' => $this->getActiveSchoolYearCode(),
            ]);

            if (!$components->isEmpty()) {
                foreach ($components as $item) {
                    PaymentDetail::create([
                        'prr_id' => $payment->prr_id,
                        'prrd_component' => $item->component->msc_name,
                        'prrd_amount' => $item->cd_fee,
                        'is_plus' => 1,
                        'type' => 'component',
                    ]);
                }
            }
            if($default){
                PaymentDetail::create([
                    'prr_id' => $payment->prr_id,
                    'prrd_component' => 'Pembayaran Cuti',
                    'prrd_amount' => (int)$default,
                    'is_plus' => 1,
                    'type' => 'component',
                ]);
            }

            $payment = LeaveReceiver::create([
                'student_number' => $student->student_number,
                'msy_code' => $this->getActiveSchoolYearCode(),
                'mlr_nominal' => $prr_total,
                'mlr_status' => $bool_status,
                'prr_id' => $payment->prr_id,
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e->getMessage());
        }
        $text = "Berhasil generate tagihan cuti mahasiswa " . $student->fullname;
        return json_encode(array('success' => true, 'message' => $text));
    }
}
