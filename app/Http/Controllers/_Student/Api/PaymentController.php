<?php

namespace App\Http\Controllers\_Student\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Authentication\StaticStudentUser;
use App\Models\Payment\Payment;
use Illuminate\Support\Facades\DB;

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
}
