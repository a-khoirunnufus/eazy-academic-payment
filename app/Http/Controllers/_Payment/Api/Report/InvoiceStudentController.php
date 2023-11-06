<?php

namespace App\Http\Controllers\_Payment\Api\Report;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Models\Payment\Payment;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\Year;
use App\Traits\Payment\General as PaymentGeneral;
use App\Traits\Models\DatatableManualFilter;
use App\Traits\Models\DatatableManualSort;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class InvoiceStudentController extends Controller
{
    use PaymentGeneral, DatatableManualFilter, DatatableManualSort;

    function oldStudentPerStudyprogram(Request $request)
    {
        $validated = $request->validate([
            'school_year' => 'required',
        ]);

        $school_year = $validated['school_year'];
        $year = Year::where('msy_code', $school_year)->first()->toArray();

        $query = Studyprogram::with(['faculty']);

        $datatable = datatables($query);

        $datatable->addColumn('school_year', function() use($year) {
            return $year;
        });

        $datatable->addColumn('invoice_summary', function($studyprogram) use($school_year) {
            $total_invoice = 0;
            $total_invoice_paid_off = 0;
            $total_invoice_not_paid_off = 0;

            $payments = $this->getStudyprogramPaymentsWithCache($school_year, $studyprogram);

            foreach ($payments as $payment) {
                $total_invoice++;
                if ($payment->computed_payment_status == 'lunas') {
                    $total_invoice_paid_off++;
                } else {
                    $total_invoice_not_paid_off++;
                }
            }

            return [
                'total' => $total_invoice,
                'paid_off' => $total_invoice_paid_off,
                'not_paid_off' => $total_invoice_not_paid_off,
            ];
        });

        $datatable->addColumn('invoice_component', function($studyprogram) use($school_year) {
            $component_total_amount = 0;

            $payments = $this->getStudyprogramPaymentsWithCache($school_year, $studyprogram);

            foreach ($payments as $payment) {
                $component_total_amount += $payment->computed_component_total_amount;
            }

            return $component_total_amount;
        });

        $datatable->addColumn('invoice_penalty', function($studyprogram) use($school_year) {
            $penalty_total_amount = 0;

            $payments = $this->getStudyprogramPaymentsWithCache($school_year, $studyprogram);

            foreach ($payments as $payment) {
                $penalty_total_amount += $payment->computed_penalty_total_amount;
            }

            return $penalty_total_amount;
        });

        $datatable->addColumn('invoice_scholarship', function($studyprogram) use($school_year) {
            $scholarship_total_amount = 0;

            $payments = $this->getStudyprogramPaymentsWithCache($school_year, $studyprogram);

            foreach ($payments as $payment) {
                $scholarship_total_amount += $payment->computed_scholarship_total_amount;
            }

            return $scholarship_total_amount;
        });

        $datatable->addColumn('invoice_discount', function($studyprogram) use($school_year) {
            $discount_total_amount = 0;

            $payments = $this->getStudyprogramPaymentsWithCache($school_year, $studyprogram);

            foreach ($payments as $payment) {
                $discount_total_amount += $payment->computed_discount_total_amount;
            }

            return $discount_total_amount;
        });

        $datatable->addColumn('admin_cost', function($studyprogram) use($school_year) {
            $admin_cost = 0;

            $payments = $this->getStudyprogramPaymentsWithCache($school_year, $studyprogram);

            foreach ($payments as $payment) {
                $admin_cost += $payment->computed_admin_cost;
            }

            return $admin_cost;
        });

        $datatable->addColumn('final_bill', function($studyprogram) use($school_year) {
            $final_bill = 0;

            $payments = $this->getStudyprogramPaymentsWithCache($school_year, $studyprogram);

            foreach ($payments as $payment) {
                $final_bill += $payment->computed_final_bill;
            }

            return $final_bill;
        });

        $datatable->addColumn('total_paid', function($studyprogram) use($school_year) {
            $total_paid = 0;

            $payments = $this->getStudyprogramPaymentsWithCache($school_year, $studyprogram);

            foreach ($payments as $payment) {
                $total_paid += $payment->computed_total_paid;
            }

            return $total_paid;
        });

        $datatable->addColumn('total_not_paid', function($studyprogram) use($school_year) {
            $total_not_paid = 0;

            $payments = $this->getStudyprogramPaymentsWithCache($school_year, $studyprogram);

            foreach ($payments as $payment) {
                $total_not_paid += $payment->computed_total_not_paid;
            }

            return $total_not_paid;
        });

        $this->applyManualFilter(
            $datatable,
            $request,
            [
                // filter attributes
                'faculty_id',
                'studyprogram_id',
            ],
            [
                // search attributes
                'studyprogram_name',
                'studyprogram_type',
                'faculty.faculty_name',
            ],
        );

        $datatable_array = $this->applyManualSort(
            $datatable,
            $request,
            [
                // sort attributes
                'school_year.msy_code',
                'studyprogram_name',
                'invoice_summary.total',
                'invoice_component',
                'invoice_penalty',
                'invoice_scholarship',
                'invoice_discount',
                'final_bill',
                'total_paid',
                'total_not_paid'
            ]
        );

        return response()->json($datatable_array);
    }

    private function getStudyprogramPaymentsWithCache($school_year, $studyprogram)
    {
        $payments = Cache::remember('finance.report.payments.'.$school_year.'.'.$studyprogram->studyprogram_id, 60*1, function () use($school_year, $studyprogram) {
            return Payment::with([
                    'student',
                    'student.studyProgram',
                    'dispensation',
                    'credit',
                    'paymentBill',
                    'paymentBill.paymentTransaction',
                    'paymentDetail',
                ])
                ->where('prr_school_year', $school_year)
                ->whereHas('student.studyProgram', function($q) use($studyprogram) {
                    $q->where('studyprogram_id', $studyprogram->studyprogram_id);
                })
                ->whereNotNull('student_number')
                ->get();
        });

        return $payments;
    }

    function oldStudentPerStudent(Request $request)
    {
        $validated = $request->validate([
            'school_year' => 'required',
        ]);

        $query = Payment::with([
                'student',
                'student.path',
                'student.period',
                'student.studyProgram',
                'student.studyProgram.faculty',
                'year',
            ])
            ->whereNotNull('student_number')
            ->where('prr_school_year', $validated['school_year']);

        $datatable = datatables($query);

        $datatable->addColumn('invoice_component', function($payment) {
            return [
                'list' => $payment->computed_component_list->toArray(),
                'total' => $payment->computed_component_total_amount
            ];
        });

        $datatable->addColumn('invoice_penalty', function($payment) {
            return [
                'list' => $payment->computed_penalty_list->toArray(),
                'total' => $payment->computed_penalty_total_amount
            ];
        });

        $datatable->addColumn('invoice_scholarship', function($payment) {
            return [
                'list' => $payment->computed_scholarship_list->toArray(),
                'total' => $payment->computed_scholarship_total_amount
            ];
        });

        $datatable->addColumn('invoice_discount', function($payment) {
            return [
                'list' => $payment->computed_discount_list->toArray(),
                'total' => $payment->computed_discount_total_amount
            ];
        });

        $datatable->addColumn('admin_cost', function($payment) {
            return $payment->computed_admin_cost;
        });

        $datatable->addColumn('final_bill', function($payment) {
            return $payment->computed_final_bill;
        });

        $datatable->addColumn('total_paid', function($payment) {
            return $payment->computed_total_paid;
        });

        $datatable->addColumn('total_not_paid', function($payment) {
            return $payment->computed_total_not_paid;
        });

        $this->applyManualFilter(
            $datatable,
            $request,
            [
                // filter attributes
                'student.student_school_year',
                'student.period_id',
                'student.path_id',
                'student.studyProgram.faculty_id',
                'student.studyprogram_id',
                'prr_status',
            ],
            [
                // search attributes
                'year.msy_year',
                'student.studyProgram.studyprogram_name',
                'student.studyProgram.studyprogram_type',
                'student.studyProgram.faculty.faculty_name',
                'student.fullname',
                'student.period.period_name',
                'student.path.path_name',
                'prr_status',
            ],
        );

        $datatable_array = $this->applyManualSort(
            $datatable,
            $request,
            [
                // sort attributes
                'prr_id',
                'prr_school_year',
                'student.fullname',
                'student.study_program.studyprogram_name',
                'invoice_component.total',
                'invoice_penalty.total',
                'invoice_scholarship.total',
                'invoice_discount.total',
                'final_bill',
                'total_paid',
                'total_not_paid',
                'prr_status',
            ]
        );

        return response()->json($datatable_array);
    }

    function oldStudentHistory($student_number, Request $request)
    {
        $search = $request->get('search_filter');
        $data = $this->getColomns('prrb.*')->where('ms2.student_number', '=', $student_number)->distinct()->get();
        foreach ($data as $items) {
            $items->method = DB::select('SELECT prr_method FROM finance.payment_re_register WHERE prr_id = ?', [$items->prr_id])[0]->prr_method;
        }

        if ($search !== '#ALL' && $search !== NULL) {
            $data_filter = [];
            foreach ($data as $list) {
                $row = json_encode($list);
                if (strpos($row, $search)) {
                    array_push($data_filter, $list);
                }
            }
            return DataTables($data_filter)->toJson();
        }

        return DataTables($data)->toJson();
    }

    function exportOldStudentPerProdi(Request $request)
    {
        $textData = $request->post('data');
        $data = json_decode($textData);
        $type = $request->get('old');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //header table
        $sheet->setCellValue('A1', 'PROGRAM STUDI');
        $sheet->mergeCells('A1:A2');

        $sheet->setCellValue('B1', 'MAHASISWA');
        $sheet->mergeCells('B1:D1');
        $sheet->setCellValue('B2', 'LUNAS');
        $sheet->setCellValue('C2', 'BELUM LUNAS');
        $sheet->setCellValue('D2', 'JUMLAH MAHASISWA');

        $sheet->setCellValue('E1', 'RINCIAN');
        $sheet->mergeCells('E1:H1');
        $sheet->setCellValue('E2', 'TAGIHAN');
        $sheet->setCellValue('F2', 'DENDA');
        $sheet->setCellValue('G2', 'BEASISWA');
        $sheet->setCellValue('H2', 'POTONGAN');

        $sheet->setCellValue('I1', 'PEMBAYARAN');
        $sheet->mergeCells('I1:K1');
        $sheet->setCellValue('I2', 'TOTAL HARUS BAYAR');
        $sheet->setCellValue('J2', 'TERBAYAR');
        $sheet->setCellValue('K2', 'PIUTANG');

        //content table
        $baris = 3;
        foreach ($data as $item){
            $row = $item;
            $total_tagihan = 0;
            $total_denda = 0;
            $total_beasiswa = 0;
            $total_potongan = 0;
            $total_terbayar = 0;
            $total_harus_bayar = 0;
            $total_piutang = 0;
            $total_mahasiswa = 0;
            $total_mahasiswa_lunas = 0;
            $total_mahasiswa_belum_lunas = 0;

            $total_mahasiswa = count($row->student);
            for ($j = 0; $j < count($row->student); $j++) {
                $total_tagihan += $row->student[$j]->payment->prr_amount;
                $total_denda += $row->student[$j]->payment->penalty;
                $total_beasiswa += $row->student[$j]->payment->schoolarsip;
                $total_potongan += $row->student[$j]->payment->discount;
                $total_harus_bayar += $row->student[$j]->payment->prr_total;
                $total_terbayar += $row->student[$j]->payment->prr_paid;
                $total_piutang += $total_harus_bayar - $total_terbayar;

                if ($row->student[$j]->payment->prr_total - $row->student[$j]->payment->prr_paid > 0) {
                    $total_mahasiswa_belum_lunas++;
                } else {
                    $total_mahasiswa_lunas++;
                }
            }

            $sheet->setCellValue('A'.$baris, $item->studyprogram_type.' '.$item->studyprogram_name);
            $sheet->setCellValue('B'.$baris, $total_mahasiswa_lunas);
            $sheet->setCellValue('C'.$baris, $total_mahasiswa_belum_lunas);
            $sheet->setCellValue('D'.$baris, $total_mahasiswa);
            $sheet->setCellValue('E'.$baris, $total_tagihan);
            $sheet->setCellValue('F'.$baris, $total_denda);
            $sheet->setCellValue('G'.$baris, $total_beasiswa);
            $sheet->setCellValue('H'.$baris, $total_potongan);
            $sheet->setCellValue('I'.$baris, $total_harus_bayar);
            $sheet->setCellValue('J'.$baris, $total_terbayar);
            $sheet->setCellValue('K'.$baris, $total_piutang);
            $baris++;
        }

        $response = response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Laporan Mahasiswa Program Study.xlsx"');
        $response->send();
    }

    private function getColomns()
    {
        $list_colomns = func_get_args();
        return DB::table('masterdata.ms_studyprogram as ms')
            ->select($list_colomns)
            ->join('hr.ms_student as ms2', 'ms2.studyprogram_id', '=', 'ms.studyprogram_id')
            ->join('finance.payment_re_register as prr', 'prr.student_number', '=', 'ms2.student_number')
            ->join('masterdata.ms_school_year as msy', 'msy.msy_id', '=', 'ms2.msy_id')
            ->join('finance.payment_re_register_detail as prrd', 'prrd.prr_id', '=', 'prr.prr_id')
            ->join('finance.payment_re_register_bill as prrb', 'prrb.prr_id', '=', 'prr.prr_id')
            ->whereNull('prr.deleted_at')
            ->whereNull('prrd.deleted_at')
            ->whereNull('prrb.deleted_at');
    }
}
