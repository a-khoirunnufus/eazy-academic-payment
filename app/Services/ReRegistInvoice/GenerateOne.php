<?php

namespace App\Services\ReRegistInvoice;

use Illuminate\Support\Facades\DB;
use App\Exceptions\GenerateInvoiceException;
use App\Models\PMB\Register;
use App\Models\PMB\Setting;
use App\Models\Payment\ComponentDetail;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentDetail;
use App\Models\Payment\PaymentBill;

class GenerateOne {

    private static $force_generate = false;

    public static function generate(int $invoice_period_code, int $register_id)
    {
        /**
         * EAZY SERVICE COST DISABLED
         */

        $register = Register::find($register_id);

        // check participant not exist
        if($register->participant == null) {
            throw new GenerateInvoiceException('Participant not found!', 500);
        }

        $invoice_components = ComponentDetail::with('component')
            ->whereHas('component', function ($q) {
                $q->where('msc_is_new_student', '=', 1);
            })
            ->where([
                ['period_id', '=', $register->ms_period_id],
                ['path_id', '=', $register->ms_path_id],
                ['mma_id', '=', $register->reg_major_pass],
                ['mlt_id', '=', intval($register->reg_major_lecture_type_pass)],
            ])
            ->get();

        // check invoice component not defined yet
        if (
            $invoice_components->count() == 0
            && !self::$force_generate
        ) {
            throw new GenerateInvoiceException('Invoice component not found!', 500);
        }

        // eazy service cost
        // $eazy_service_cost = Setting::where('setting_key', 'biaya_service_eazy')->first()->setting_value;

        // total invoice
        $invoice_total = 0;
        foreach($invoice_components as $item){
            $invoice_total = $invoice_total + $item->cd_fee;
        }

        // partner's net income
        $partner_net_income = $invoice_total; //- intval($eazy_service_cost);

        try {
            DB::beginTransaction();

            // insert payment_re_register record
            $payment = Payment::create([
                'reg_id' => $register->reg_id,
                'prr_status' => 'belum lunas',
                'prr_total' => $invoice_total,
                'prr_paid_net' => $partner_net_income,
                'prr_school_year' => $invoice_period_code,
                'par_id' => $register->par_id,
            ]);

            // insert payment_re_register_detail records
            foreach($invoice_components as $item){
                PaymentDetail::create([
                    'prr_id' => $payment->prr_id,
                    'prrd_component' => $item->component->msc_name,
                    'prrd_amount' => $item->cd_fee,
                    'is_plus' => 1,
                    'type' => 'component',
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            throw new GenerateInvoiceException($th->getMessage(), 500);
        }
    }
}
