<?php

namespace App\Http\Controllers\_Payment\Api\Report;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Payment\Faculty;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentBill;
use App\Models\Payment\Year;
use App\Models\Payment\Settings;
use App\Models\Admission\Payment as AdmissionPayment;
use App\Models\Admission\PaymentBill as AdmissionPaymentBill;
use App\Traits\Models\QueryFilterExtendByRequest;
use App\Traits\Payment\General;
use App\Exports\Payment\Report\InvoiceNewStudentPerStudentExport;
use App\Exports\Payment\Report\InvoiceNewStudentPerStudyprogramExport;

use Maatwebsite\Excel\Excel;

class InvoiceNewStudentController extends Controller
{
    use General, QueryFilterExtendByRequest;

    private $source = '';
    private $table_source = '';
    private $table_source_studyprogram = '';

    public function __construct()
    {
        $setting_source = Settings::where('name', 'payment_report_invoice_new_student_source')->first()->value;

        if ($setting_source == 'finance') {
            $this->source = 'finance';
            $this->table_source = 'finance.vw_invoice_new_student_finance_master';
            $this->table_source_studyprogram = 'finance.vw_invoice_new_student_finance_per_studyprogram';
        }
        elseif ($setting_source == 'admission') {
            $this->source = 'admission';
            $this->table_source = 'finance.vw_invoice_new_student_admission_master';
            $this->table_source_studyprogram = 'finance.vw_invoice_new_student_admission_per_studyprogram';
        }
    }

    public function perStudentDatatable(Request $request)
    {
        $query = DB::table($this->table_source);

        $this->applyFilterWoFc(
            $query,
            $request,
            [
                'invoice_nominal_total',
                'invoice_component_total_amount',
                'invoice_penalty_total_amount',
                'invoice_scholarship_total_amount',
                'invoice_discount_total_amount',
                'payment_admin_cost',
                'payment_total_paid',
                'payment_total_unpaid',
                'payment_status',
                'registration_year_id',
                'registration_period_id',
                'registration_path_id',
                'registration_major_id',
                'registration_faculty_id',
                'registration_major_lecture_type_id',
            ]
        );

        $datatable = datatables($query);

        return $datatable->toJson();
    }

    public function perStudentRefreshInfo()
    {
        if ($this->source == 'finance') {
            $info = DB::table('finance.log_view_refresh_info')
                ->where('view_name', 'finance.vw_invoice_new_student_finance_master')
                ->first();
        }

        if ($this->source == 'admission') {
            $info = DB::table('finance.log_view_refresh_info')
                ->where('view_name', 'finance.vw_invoice_new_student_admission_master')
                ->first();
        }

        return response()->json($info);
    }

    public function perStudentRefresh()
    {
        try {
            if ($this->source == 'finance') {
                DB::statement('REFRESH MATERIALIZED VIEW finance.vw_invoice_new_student_finance_master');
                DB::table('finance.log_view_refresh_info')
                    ->where('view_name', 'finance.vw_invoice_new_student_finance_master')
                    ->update(['last_refresh_time' => $this->getCurrentDateTime()]);
            }

            if ($this->source == 'admission') {
                DB::statement('REFRESH MATERIALIZED VIEW finance.vw_invoice_new_student_admission_master');
                DB::table('finance.log_view_refresh_info')
                    ->where('view_name', 'finance.vw_invoice_new_student_admission_master')
                    ->update(['last_refresh_time' => $this->getCurrentDateTime()]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil memperbaharui data.'
        ]);
    }

    public function perStudentExport(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:csv,excel',
            'filter' => 'nullable',
        ]);

        $options = [
            'file_name' => 'invoice_new_student.xlsx',
            'source' => $this->source,
            'filters' => $validated['filter'] ?? [],
        ];

        if ($validated['type'] == 'csv') {
            $options['writer_type'] = Excel::CSV;
            $options['headers'] = [
                'Content-Type' => 'text/csv',
            ];
        }
        elseif ($validated['type'] == 'excel') {
            $options['writer_type'] = Excel::XLSX;
            $options['headers'] = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ];
        }

        return new InvoiceNewStudentPerStudentExport($options);
    }

    public function invoiceBillDatatable(Request $request)
    {
        $invoice_id = $request->get('invoice_id');

        if ($this->source == 'finance') {
            $bills = PaymentBill::where('prr_id', $invoice_id)
                ->get()
                ->toArray();
        }

        if ($this->source == 'admission') {
            $bills = AdmissionPaymentBill::where('prr_id', $invoice_id)
                ->get()
                ->toArray();
            $bills = array_map(function($item) {
                return [
                    ...$item,
                    'prrb_due_date' => $item['prrb_expired_date'],
                ];
            }, $bills);
        }

        return datatables($bills)->toJson();
    }

    public function perStudyprogramDatatable(Request $request)
    {
        $query = DB::table($this->table_source_studyprogram);

        $this->applyFilterWoFc(
            $query,
            $request,
            [
                'registration_year_id',
                'registration_period_id',
                'registration_path_id',
                'registration_major_id',
                'registration_faculty_id',
                'registration_major_lecture_type_id',
                'invoice_nominal_total',
            ]
        );

        $datatable = datatables($query);

        return $datatable->toJson();
    }

    public function perStudyprogramRefreshInfo()
    {
        if ($this->source == 'finance') {
            $info = DB::table('finance.log_view_refresh_info')
                ->where('view_name', 'finance.vw_invoice_new_student_finance_per_studyprogram')
                ->first();
        }

        if ($this->source == 'admission') {
            $info = DB::table('finance.log_view_refresh_info')
                ->where('view_name', 'finance.vw_invoice_new_student_admission_per_studyprogram')
                ->first();
        }

        return response()->json($info);
    }

    public function perStudyprogramRefresh()
    {
        \Log::debug($this->getCurrentDateTime());
        try {
            if ($this->source == 'finance') {
                DB::statement('REFRESH MATERIALIZED VIEW finance.vw_invoice_new_student_finance_per_studyprogram');
                DB::table('finance.log_view_refresh_info')
                    ->where('view_name', 'finance.vw_invoice_new_student_finance_per_studyprogram')
                    ->update(['last_refresh_time' => $this->getCurrentDateTime()]);
            }

            if ($this->source == 'admission') {
                DB::statement('REFRESH MATERIALIZED VIEW finance.vw_invoice_new_student_admission_per_studyprogram');
                DB::table('finance.log_view_refresh_info')
                    ->where('view_name', 'finance.vw_invoice_new_student_admission_per_studyprogram')
                    ->update(['last_refresh_time' => $this->getCurrentDateTime()]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil memperbaharui data.'
        ]);
    }

    public function perStudyprogramExport(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:csv,excel',
            'filter' => 'nullable',
        ]);

        $options = [
            'file_name' => 'invoice_new_student.xlsx',
            'source' => $this->source,
            'filters' => $validated['filter'] ?? [],
        ];

        if ($validated['type'] == 'csv') {
            $options['writer_type'] = Excel::CSV;
            $options['headers'] = [
                'Content-Type' => 'text/csv',
            ];
        }
        elseif ($validated['type'] == 'excel') {
            $options['writer_type'] = Excel::XLSX;
            $options['headers'] = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ];
        }

        return new InvoiceNewStudentPerStudyprogramExport($options);
    }
}
