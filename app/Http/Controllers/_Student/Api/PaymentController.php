<?php

namespace App\Http\Controllers\_Student\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Authentication\StaticStudentUser;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentDetail;
use App\Models\Payment\PaymentBill;
use App\Models\Masterdata\MsPaymentMethod;
use App\Models\PMB\Setting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * @var $default_user_email
     * @func getStaticUser()
     */
    use StaticStudentUser;

    public function unpaidPayment(Request $request) {
        /**
         * NOTE:
         * - invoice number still using prr_id, change later.
         */

        $validated = $request->validate([
            'participant_id' => 'required',
        ]);

        $invoices = DB::table('finance.payment_re_register_bill as prrb')
            ->leftJoin('finance.payment_re_register as prr', 'prr.prr_id', '=', 'prrb.prr_id')
            ->leftJoin('finance.payment_re_register_detail as prrd', 'prrd.prr_id', '=', 'prr.prr_id')
            ->leftJoin('masterdata.ms_school_year as msy', 'msy.msy_code', '=', DB::raw("prr.prr_school_year::VARCHAR"))
            ->where([
                ['par_id', '=', $validated['participant_id']],
                ['prrb.prrb_status', '=', 'belum lunas'],
                ['prr.deleted_at', 'is', DB::raw("NULL")],
                ['prrb.deleted_at', 'is', DB::raw("NULL")],
                ['prrb.prrb_paid_date', 'is', DB::raw("NULL")]
            ])
            ->select(
                'prr.prr_id as prr_id',
                'msy.msy_year as invoice_school_year_year',
                'msy.msy_semester as invoice_school_year_semester',
                DB::raw("'INV/' || prr.prr_id as invoice_number"),
                'prr.created_at as invoice_issued_date',
                DB::raw("NULL as month"),
                'prrb.prrb_expired_date as payment_due_date',
                DB::raw("
                    CASE
                        WHEN prrd.type = 'component' THEN
                            '[' || STRING_AGG('{\"name\":\"'::TEXT || prrd.prrd_component || '\",\"nominal\":'::TEXT || prrd.prrd_amount || '}'::TEXT, ',') || ']'
                        ELSE
                            '[]'::TEXT
                    END as invoice_detail
                "),
                DB::raw("
                    CASE
                        WHEN prrd.type = 'beasiswa' THEN
                            '[' || STRING_AGG('{\"name\":\"'::TEXT || prrd.prrd_component || '\",\"nominal\":'::TEXT || prrd.prrd_amount || '}'::TEXT, ',') || ']'
                        ELSE
                            '[]'::TEXT
                    END as scholarship_detail
                "),
                DB::raw("
                    CASE
                        WHEN prrd.type = 'potongan' THEN
                            '[' || STRING_AGG('{\"name\":\"'::TEXT || prrd.prrd_component || '\",\"nominal\":'::TEXT || prrd.prrd_amount || '}'::TEXT, ',') || ']'
                        ELSE
                            '[]'::TEXT
                    END as discount_detail
                "),
                DB::raw("
                    CASE
                        WHEN prrd.type = 'denda' THEN
                            '[' || STRING_AGG('{\"name\":\"'::TEXT || prrd.prrd_component || '\",\"nominal\":'::TEXT || prrd.prrd_amount || '}'::TEXT, ',') || ']'
                        ELSE
                            '[]'::TEXT
                    END as penalty_detail
                "),
                'prrb.prrb_amount as total_amount',
                'prr.prr_method as payment_method'
            )
            ->distinct()
            ->groupBy('prr.prr_id', 'msy.msy_year', 'msy.msy_semester', 'prrb.prrb_expired_date', 'prrd.type', 'prrb.prrb_amount')
            ->orderBy('prr.prr_id', 'asc')
            ->get()
            ->toArray();

        $datatable = datatables($invoices);

        return $datatable->toJSON();
    }

    public function paidPayment() {
        $data = [
            [
                'id' => 1,
                'period' => '2022/2023',
                'semester' => 'Semester Genap',
                'invoice_code' => 'INV/20192/2010210',
                'month' => 'Januari - Februari',
                'nth_installment' => 1,
                'payment_method_name' => 'VA BNI',
                'payment_method_detail' => [
                    ['label' => 'Kode', 'value' => '002201923123'],
                    ['label' => 'Tanggal', 'value' => '01-02-2023 / 11:05:00'],
                ],
                'invoice_total' => 2300000,
                'payment_total' => 2300000,
                'is_paid_off' => true,
            ],
        ];

        $datatable = datatables($data);

        return $datatable->toJSON();
    }

    public function selectMethod(Request $request) {
        $validated = $request->validate([
            'prr_id' => 'required',
            'method' => 'required|in:bca,mandiri,bni',
        ]);

        try {
            DB::beginTransaction();

            $payment_method = MsPaymentMethod::where('mpm_key', $validated['method'])->first();
            $eazy_service_cost = Setting::where('setting_key', 'biaya_service_eazy')->first()->setting_value;

            $invoice_total_net = 0;
            $payment_detail = PaymentDetail::where('prr_id', '=', $validated['prr_id'])->get();
            foreach($payment_detail as $payment_item) {
                if ($payment_item->is_plus == 1) {
                    $invoice_total_net += $payment_item->prrd_amount;
                } else {
                    $invoice_total_net -= $payment_item->prrd_amount;
                }
            }

            $invoice_total = $invoice_total_net + intval($payment_method->mpm_fee);
            $partner_net_income = $invoice_total_net - intval($eazy_service_cost);

            $payment = Payment::find($validated['prr_id']);
            $payment->prr_method = $validated['method'];
            $payment->prr_total = $invoice_total;
            $payment->prr_paid_net = $partner_net_income;
            $payment->save();

            PaymentBill::where('prr_id', '=', $payment->prr_id)->update([
                'prrb_invoice_num' => $payment_method->mpm_account_number,
                'prrb_expired_date' => Carbon::now()->addHours(24)->format('Y-m-d'),
                'prrb_admin_cost' => $payment_method->mpm_fee,
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasl memilih metode pembayaran',
        ], 200);
    }
}
