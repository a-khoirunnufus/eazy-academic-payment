<?php

namespace App\Http\Controllers\_Payment\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Models\Payment\Faculty;
use App\Models\Payment\Payment;
use App\Models\PMB\Register;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\Year;
use App\Traits\Payment\General as PaymentGeneral;
use App\Traits\Models\DatatableManualFilter;
use App\Traits\Models\DatatableManualSort;

use Yajra\DataTables\DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportControllerApi extends Controller
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

    function newStudent(Request $request)
    {
        // $list_studyProgram = $this->getColomns('ms.studyprogram_id', 'ms.studyprogram_type', 'ms.studyprogram_name')->distinct()->get();
        $year = Year::query();
        if ($request->get('data_filter') !== '#ALL' && $request->get('data_filter') !== NULL) {
            $year = $year->where('msy_id', '=', $request->get('data_filter'));
        }
        $year = $year->get();
        $data = [];
        $id_faculty = $request->get('id_faculty');
        $id_prodi = $request->get('id_prodi');

        $dataStudent = [];
        $spesifikProdi = $request->get('prodi');
        $prodi_filter_angkatan = $request->get('prodi_filter_angkatan');
        $prodi_search_filter = $request->get('prodi_search_filter');
        $prodi_path_filter = $request->get('prodi_path_filter');
        $prodi_period_filter = $request->get('prodi_period_filter');

        foreach ($year as $tahun) {
            $list_studyProgram = $this->getNew('ms.*')->where('msy.msy_id', '=', $tahun->msy_id);
            if ($spesifikProdi !== '#ALL' && $spesifikProdi !== NULL) {
                $list_studyProgram = $list_studyProgram->where('ms.studyprogram_id', '=', $spesifikProdi);
            }
            if ($id_faculty !== '#ALL' && $id_faculty !== NULL) {
                $list_studyProgram = $list_studyProgram->where('ms.faculty_id', '=', $id_faculty);
            }
            if ($id_prodi !== '#ALL' && $id_prodi !== NULL) {
                $list_studyProgram = $list_studyProgram->where('ms.studyprogram_id', '=', $id_prodi);
            }
            $list_studyProgram = $list_studyProgram->distinct()->get();

            foreach ($list_studyProgram as $studyProgram) {
                // $listStudent = $this->getColomns('ms2.*','ms2.student_id', 'ms2.fullname');
                $prr_list_reg_id = DB::table('finance.payment_re_register as prr')
                                            ->whereNull('prr.deleted_at')
                                            ->whereNotNull('prr.reg_id')
                                            ->select('prr.reg_id');
                $list_reg_id = array();
                foreach($prr_list_reg_id->get() as $item){
                    array_push($list_reg_id, $item->reg_id);
                }
                $listStudent = $this->getNew(
                    'p.par_id',
                    'p.par_fullname',
                    'p.par_nik',
                    'p.par_phone',
                    'p.par_birthday',
                    'p.par_gender',
                    'p.par_religion',
                    'r.reg_id',
                    'r.ms_period_id',
                    'r.ms_path_id'
                )->whereIn('r.reg_id', $list_reg_id);
                if ($prodi_filter_angkatan !== '#ALL' && $prodi_filter_angkatan !== NULL) {
                    $listStudent->where(DB::raw("SUBSTR(TO_CHAR(r.reg_major_pass_date, 'YYYY-MM-DD'), 1, 4)"), '=', $prodi_filter_angkatan);
                }
                if ($prodi_path_filter !== '#ALL' && $prodi_path_filter !== NULL) {
                    $listStudent->where('r.ms_path_id', '=', $prodi_path_filter);
                }
                if ($prodi_period_filter !== '#ALL' && $prodi_period_filter !== NULL) {
                    $listStudent->where('r.ms_period_id', '=', $prodi_period_filter);
                }
                $listStudent->where('ms.studyprogram_id', '=', $studyProgram->studyprogram_id)->where('msy.msy_id', '=', $tahun->msy_id)->distinct();

                $studyProgram->student = $listStudent->get();
                $studyProgram->year = $tahun;
                $studyProgram->faculty = Faculty::where('faculty_id', '=', $studyProgram->faculty_id)->get();

                foreach ($studyProgram->student as $list_student) {
                    $listPayment = $this->getNew('prr.*')
                        ->where('prr.reg_id', '=', $list_student->reg_id)
                        ->distinct()->get();

                    $denda = 0;
                    $beasiswa = 0;
                    $potongan = 0;
                    foreach ($listPayment as $list_payment) {
                        $listPaymentDetail = $this->getNew('prrd.*')
                            ->where('prrd.prr_id', '=', $list_payment->prr_id)
                            ->distinct()
                            ->get();
                        $total_prrd_amount = 0;
                        foreach ($listPaymentDetail as $pd) {
                            switch ($pd->type) {
                                case "component":
                                    $total_prrd_amount += $pd->prrd_amount;
                                    break;
                                case "denda":
                                    $denda += $pd->prrd_amount;
                                    break;
                                case "scholarship":
                                    $beasiswa += $pd->prrd_amount;
                                    break;
                                case "discount":
                                    $potongan += $pd->prrd_amount;
                                    break;
                            }
                        }

                        $listPaymentBill = $this->getNew('prrb.*')
                            ->where('prrb.prr_id', '=', $list_payment->prr_id)
                            ->distinct()
                            ->get();
                        $total_paid = 0;
                        foreach ($listPaymentBill as $pb) {
                            $manual_payment = DB::table('finance.payment_re_register_transaction')
                                                ->where('prrb_id', '=', $pb->prrb_id)
                                                ->whereNull('deleted_at')
                                                ->get();
                            if(count($manual_payment) > 0){
                                $total = 0;
                                foreach($manual_payment as $mp){
                                    $total += $mp->prrt_amount;
                                }
                                if($total >= $pb->prrb_amount){
                                    $pb_update = DB::table('finance.payment_re_register_bill')
                                                ->where('prrb_id', '=', $pb->prrb_id)
                                                ->update(['prrb_status' => 'lunas']);
                                }
                                $total_paid += $total;
                            }
                            if(count($manual_payment) < 1){
                                $total_paid += $pb->prrb_status == 'lunas' ? $pb->prrb_amount : 0;
                            }
                        }

                        $list_payment->payment_detail = $listPaymentDetail;
                        $list_payment->payment_bill = $listPaymentBill;
                        $list_payment->prr_total = ($total_prrd_amount + $denda) - ($beasiswa + $potongan);
                        $list_payment->prr_amount = $total_prrd_amount + $denda;
                        $list_payment->prr_paid = $total_paid;
                        $list_payment->penalty = $denda;
                        $list_payment->schoolarsip = $beasiswa;
                        $list_payment->discount = $potongan;
                        $list_student->payment = $list_payment;
                    }

                    if ($spesifikProdi !== '#ALL' && $spesifikProdi !== NULL) {
                        $detail_prodi = $studyProgram;
                        unset($detail_prodi->student);
                        $list_student->studyprogram = $detail_prodi;
                        array_push($dataStudent, $list_student);
                    }
                }
                array_push($data, $studyProgram);
            }
        }

        if ($spesifikProdi !== '#ALL' && $spesifikProdi !== NULL) {
            if ($prodi_search_filter !== '#ALL' && $prodi_search_filter !== NULL) {
                $data_filter = [];
                foreach ($dataStudent as $list) {
                    $row = json_encode($list);
                    if (strpos($row, $prodi_search_filter)) {
                        array_push($data_filter, $list);
                    }
                }
                return DataTables($data_filter)->toJson();
            }
            return DataTables($dataStudent)->toJson();
        }

        if ($request->get('search_filter') !== '#ALL' && $request->get('search_filter') !== NULL) {
            $data_filter = [];
            foreach ($data as $list) {
                $row = json_encode($list);
                $find = $request->get('search_filter');
                if (strpos($row, $find)) {
                    array_push($data_filter, $list);
                }
            }
            return DataTables($data_filter)->toJson();
        }

        // return json_encode($data);
        return DataTables($data)->toJson();
    }

    function studentExport(Request $request)
    {
        $type = $request->get('student_export');
        if ($type == NULL) {
            return 'Error type export tidak ada';
        }
        $data = NULL;
        if ($type == 'new') {
            $data = $this->newStudent($request)->getData()->data;
        } else {
            $data = $this->oldStudent($request)->getData()->data;
        }

        // return json_encode($data);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //header table
        $sheet->setCellValue('A1', 'Fakultas');
        $sheet->mergeCells('A1:A3');
        $sheet->setCellValue('B1', 'Program Studi');
        $sheet->mergeCells('B1:B3');
        $sheet->setCellValue('C1', 'Nama Lengkap');
        $sheet->mergeCells('C1:C3');
        $sheet->setCellValue('D1', $type == 'new' ? 'NIK' : 'NIM');
        $sheet->mergeCells('D1:D3');

        $sheet->setCellValue('E1', 'Jenis Tagihan');
        $sheet->mergeCells('E1:P1');

        $sheet->setCellValue('E2', 'Tagihan');
        $sheet->mergeCells('E2:G2');
        $sheet->setCellValue('E3', 'Komponen');
        $sheet->setCellValue('F3', 'Nominal');
        $sheet->setCellValue('G3', 'Total');

        $sheet->setCellValue('H2', 'Denda');
        $sheet->mergeCells('H2:J2');
        $sheet->setCellValue('H3', 'Komponen');
        $sheet->setCellValue('I3', 'Nominal');
        $sheet->setCellValue('J3', 'Total');

        $sheet->setCellValue('K2', 'Beasiswa');
        $sheet->mergeCells('K2:M2');
        $sheet->setCellValue('K3', 'Komponen');
        $sheet->setCellValue('L3', 'Nominal');
        $sheet->setCellValue('M3', 'Total');

        $sheet->setCellValue('N2', 'Potongan');
        $sheet->mergeCells('N2:P2');
        $sheet->setCellValue('N3', 'Komponen');
        $sheet->setCellValue('O3', 'Nominal');
        $sheet->setCellValue('P3', 'Total');

        $sheet->setCellValue('Q1', 'Total Harus Bayar');
        $sheet->mergeCells('Q1:Q3');

        $sheet->setCellValue('R1', 'Pembayaran');
        $sheet->mergeCells('R1:U1');
        $sheet->setCellValue('R2', 'Nomor Tagihan');
        $sheet->mergeCells('R2:R3');
        $sheet->setCellValue('S2', 'Nominal');
        $sheet->mergeCells('S2:S3');
        $sheet->setCellValue('T2', 'Tanggal Pembayaran');
        $sheet->mergeCells('T2:T3');
        $sheet->setCellValue('U2', 'Total');
        $sheet->mergeCells('U2:U3');

        $sheet->setCellValue('V1', 'Sisa Tagihan');
        $sheet->mergeCells('V1:V3');
        $sheet->setCellValue('W1', 'Status');
        $sheet->mergeCells('W1:W3');

        $row = 4;
        foreach ($data as $items) {
            $total_tagihan = 0;
            $total_denda = 0;
            $total_beasiswa = 0;
            $total_potongan = 0;
            $total_bayar = 0;

            $row_tagihan = 0;
            $row_denda = 0;
            $row_beasiswa = 0;
            $row_potongan = 0;
            $row_bayar = 0;

            if ($type == 'new') {
                $sheet->setCellValue('A' . $row, $items->studyprogram->faculty[0]->faculty_name);
                $sheet->setCellValue('B' . $row, $items->studyprogram->studyprogram_type . ' ' . $items->studyprogram->studyprogram_name);
                $sheet->setCellValue('C' . $row, $items->par_fullname);
                $sheet->setCellValue('D' . $row, $items->par_nik);
            }else {
                $sheet->setCellValue('A' . $row, $items->studyprogram->faculty[0]->faculty_name);
                $sheet->setCellValue('B' . $row, $items->studyprogram->studyprogram_type . ' ' . $items->studyprogram->studyprogram_name);
                $sheet->setCellValue('C' . $row, $items->fullname);
                $sheet->setCellValue('D' . $row, $items->student_id);
            }

            foreach ($items->payment->payment_detail as $tagihan) {
                switch ($tagihan->type) {
                    case "component":
                        $sheet->setCellValue('E' . ($row + $row_tagihan), $tagihan->prrd_component);
                        $sheet->setCellValue('F' . ($row + $row_tagihan), $tagihan->prrd_amount);
                        $total_tagihan += $tagihan->prrd_amount;
                        $sheet->setCellValue('G' . $row, $total_tagihan);

                        $row_tagihan++;
                        break;

                    case "denda":
                        $sheet->setCellValue('H' . ($row + $row_denda), $tagihan->prrd_component);
                        $sheet->setCellValue('I' . ($row + $row_denda), $tagihan->prrd_amount);
                        $total_denda += $tagihan->prrd_amount;
                        $sheet->setCellValue('J' . $row, $total_denda);

                        $row_denda++;
                        break;

                    case "beasiswa":
                        $sheet->setCellValue('K' . ($row + $row_beasiswa), $tagihan->prrd_component);
                        $sheet->setCellValue('L' . ($row + $row_beasiswa), $tagihan->prrd_amount);
                        $total_beasiswa += $tagihan->prrd_amount;
                        $sheet->setCellValue('M' . $row, $total_beasiswa);

                        $row_beasiswa++;
                        break;

                    case "potongan":
                        $sheet->setCellValue('N' . ($row + $row_potongan), $tagihan->prrd_component);
                        $sheet->setCellValue('O' . ($row + $row_potongan), $tagihan->prrd_amount);
                        $total_potongan += $tagihan->prrd_amount;
                        $sheet->setCellValue('P' . $row, $total_potongan);

                        $row_potongan++;
                        break;
                }
            }

            $sheet->setCellValue('Q' . $row, ($total_tagihan + $total_denda) - ($total_beasiswa + $total_potongan));

            foreach ($items->payment->payment_bill as $pembayaran) {
                $sheet->setCellValue('R' . ($row + $row_bayar), $pembayaran->prrb_invoice_num);
                $sheet->setCellValue('S' . ($row + $row_bayar), $pembayaran->prrb_amount);
                $sheet->setCellValue('T' . ($row + $row_bayar), $pembayaran->prrb_paid_date);
                $total_bayar += $pembayaran->prrb_amount;
                $sheet->setCellValue('U' . $row, $total_bayar);
                $row_bayar++;
            }

            $sheet->setCellValue('V' . $row, (($total_tagihan + $total_denda) - ($total_beasiswa + $total_potongan)) - $total_bayar);
            $sheet->setCellValue('W' . $row, (($total_tagihan + $total_denda) - ($total_beasiswa + $total_potongan)) - $total_bayar > 0 ? 'Belum Lunas' : 'Lunas');

            if (
                ($row_tagihan == $row_denda &&
                    $row_tagihan == $row_beasiswa &&
                    $row_tagihan == $row_potongan &&
                    $row_tagihan == $row_bayar
                ) ||
                ($row_tagihan > $row_denda &&
                    $row_tagihan > $row_beasiswa &&
                    $row_tagihan > $row_potongan &&
                    $row_tagihan > $row_bayar
                ) ||
                ($row_tagihan == $row_denda &&
                    $row_tagihan > $row_beasiswa &&
                    $row_tagihan > $row_potongan &&
                    $row_tagihan > $row_bayar
                ) ||
                ($row_tagihan > $row_denda &&
                    $row_tagihan == $row_beasiswa &&
                    $row_tagihan > $row_potongan &&
                    $row_tagihan > $row_bayar
                ) ||
                ($row_tagihan == $row_denda &&
                    $row_tagihan == $row_beasiswa &&
                    $row_tagihan > $row_potongan &&
                    $row_tagihan > $row_bayar
                ) ||
                ($row_tagihan > $row_denda &&
                    $row_tagihan > $row_beasiswa &&
                    $row_tagihan == $row_potongan &&
                    $row_tagihan > $row_bayar
                ) ||
                ($row_tagihan == $row_denda &&
                    $row_tagihan > $row_beasiswa &&
                    $row_tagihan == $row_potongan &&
                    $row_tagihan > $row_bayar
                ) ||
                ($row_tagihan > $row_denda &&
                    $row_tagihan == $row_beasiswa &&
                    $row_tagihan == $row_potongan &&
                    $row_tagihan > $row_bayar
                ) ||
                ($row_tagihan == $row_denda &&
                    $row_tagihan == $row_beasiswa &&
                    $row_tagihan == $row_potongan &&
                    $row_tagihan > $row_bayar
                ) ||
                ($row_tagihan > $row_denda &&
                    $row_tagihan > $row_beasiswa &&
                    $row_tagihan > $row_potongan &&
                    $row_tagihan == $row_bayar
                ) ||
                ($row_tagihan == $row_denda &&
                    $row_tagihan > $row_beasiswa &&
                    $row_tagihan > $row_potongan &&
                    $row_tagihan == $row_bayar
                ) ||
                ($row_tagihan > $row_denda &&
                    $row_tagihan == $row_beasiswa &&
                    $row_tagihan > $row_potongan &&
                    $row_tagihan == $row_bayar
                ) ||
                ($row_tagihan == $row_denda &&
                    $row_tagihan == $row_beasiswa &&
                    $row_tagihan > $row_potongan &&
                    $row_tagihan == $row_bayar
                ) ||
                ($row_tagihan > $row_denda &&
                    $row_tagihan > $row_beasiswa &&
                    $row_tagihan == $row_potongan &&
                    $row_tagihan == $row_bayar
                ) ||
                ($row_tagihan == $row_denda &&
                    $row_tagihan > $row_beasiswa &&
                    $row_tagihan == $row_potongan &&
                    $row_tagihan == $row_bayar
                ) ||
                ($row_tagihan > $row_denda &&
                    $row_tagihan == $row_beasiswa &&
                    $row_tagihan == $row_potongan &&
                    $row_tagihan == $row_bayar
                )
            ) {
                $sheet->mergeCells('A' . $row . ':A' . ($row + $row_tagihan - 1));
                $sheet->mergeCells('B' . $row . ':B' . ($row + $row_tagihan - 1));
                $sheet->mergeCells('C' . $row . ':C' . ($row + $row_tagihan - 1));
                $sheet->mergeCells('D' . $row . ':D' . ($row + $row_tagihan - 1));
                $sheet->mergeCells('G' . $row . ':G' . ($row + $row_tagihan - 1));

                //H,I,J
                if ($row_denda == 0) {
                    $sheet->mergeCells('H' . $row . ':H' . ($row + $row_tagihan - 1));
                    $sheet->mergeCells('I' . $row . ':I' . ($row + $row_tagihan - 1));
                } else {
                    $sheet->mergeCells('H' . ($row + $row_denda - 1) . ':H' . ($row + $row_tagihan - 1));
                    $sheet->mergeCells('I' . ($row + $row_denda - 1) . ':I' . ($row + $row_tagihan - 1));
                }
                $sheet->mergeCells('J' . $row . ':J' . ($row + $row_tagihan - 1));
                //K,L,M
                if ($row_beasiswa == 0) {
                    $sheet->mergeCells('K' . $row . ':K' . ($row + $row_tagihan - 1));
                    $sheet->mergeCells('L' . $row . ':L' . ($row + $row_tagihan - 1));
                } else {
                    $sheet->mergeCells('K' . ($row + $row_beasiswa - 1) . ':K' . ($row + $row_tagihan - 1));
                    $sheet->mergeCells('L' . ($row + $row_beasiswa - 1) . ':L' . ($row + $row_tagihan - 1));
                }
                $sheet->mergeCells('M' . $row . ':M' . ($row + $row_tagihan - 1));
                //N,O,P
                if ($row_potongan == 0) {
                    $sheet->mergeCells('N' . $row . ':N' . ($row + $row_tagihan - 1));
                    $sheet->mergeCells('O' . $row . ':O' . ($row + $row_tagihan - 1));
                } else {
                    $sheet->mergeCells('N' . ($row + $row_potongan - 1) . ':N' . ($row + $row_tagihan - 1));
                    $sheet->mergeCells('O' . ($row + $row_potongan - 1) . ':O' . ($row + $row_tagihan - 1));
                }
                $sheet->mergeCells('P' . $row . ':P' . ($row + $row_tagihan - 1));

                $sheet->mergeCells('Q' . $row . ':Q' . ($row + $row_tagihan - 1));

                //R,S,T,U
                if ($row_bayar == 0) {
                    $sheet->mergeCells('R' . $row . ':R' . ($row + $row_tagihan - 1));
                    $sheet->mergeCells('S' . $row . ':S' . ($row + $row_tagihan - 1));
                    $sheet->mergeCells('T' . $row . ':T' . ($row + $row_tagihan - 1));
                } else {
                    $sheet->mergeCells('R' . ($row + $row_bayar - 1) . ':R' . ($row + $row_tagihan - 1));
                    $sheet->mergeCells('S' . ($row + $row_bayar - 1) . ':S' . ($row + $row_tagihan - 1));
                    $sheet->mergeCells('T' . ($row + $row_bayar - 1) . ':T' . ($row + $row_tagihan - 1));
                }
                $sheet->mergeCells('U' . $row . ':U' . ($row + $row_tagihan - 1));

                $sheet->mergeCells('V' . $row . ':V' . ($row + $row_tagihan - 1));
                $sheet->mergeCells('W' . $row . ':W' . ($row + $row_tagihan - 1));

                $row += $row_tagihan;
            }

            if (
                $row_denda > $row_tagihan &&
                $row_denda > $row_beasiswa &&
                $row_denda > $row_potongan &&
                $row_denda > $row_bayar
            ) {
                $sheet->mergeCells('A' . $row . ':A' . ($row + $row_denda - 1));
                $sheet->mergeCells('B' . $row . ':B' . ($row + $row_denda - 1));
                $sheet->mergeCells('C' . $row . ':C' . ($row + $row_denda - 1));
                $sheet->mergeCells('D' . $row . ':D' . ($row + $row_denda - 1));
                $sheet->mergeCells('J' . $row . ':J' . ($row + $row_denda - 1));

                //E,F,G TAGIHAN
                if ($row_tagihan == 0) {
                    $sheet->mergeCells('E' . $row . ':E' . ($row + $row_denda - 1));
                    $sheet->mergeCells('F' . $row . ':F' . ($row + $row_denda - 1));
                } else {
                    $sheet->mergeCells('E' . ($row + $row_tagihan - 1) . ':E' . ($row + $row_denda - 1));
                    $sheet->mergeCells('F' . ($row + $row_tagihan - 1) . ':F' . ($row + $row_denda - 1));
                }
                $sheet->mergeCells('G' . $row . ':G' . ($row + $row_denda - 1));
                //K,L,M BEASISWA
                if ($row_beasiswa == 0) {
                    $sheet->mergeCells('K' . $row . ':K' . ($row + $row_denda - 1));
                    $sheet->mergeCells('L' . $row . ':L' . ($row + $row_denda - 1));
                } else {
                    $sheet->mergeCells('K' . ($row + $row_beasiswa - 1) . ':K' . ($row + $row_denda - 1));
                    $sheet->mergeCells('L' . ($row + $row_beasiswa - 1) . ':L' . ($row + $row_denda - 1));
                }
                $sheet->mergeCells('M' . $row . ':M' . ($row + $row_denda - 1));
                //N,O,P POTONGAN
                if ($row_potongan == 0) {
                    $sheet->mergeCells('N' . $row . ':N' . ($row + $row_denda - 1));
                    $sheet->mergeCells('O' . $row . ':O' . ($row + $row_denda - 1));
                } else {
                    $sheet->mergeCells('N' . ($row + $row_potongan - 1) . ':N' . ($row + $row_denda - 1));
                    $sheet->mergeCells('O' . ($row + $row_potongan - 1) . ':O' . ($row + $row_denda - 1));
                }
                $sheet->mergeCells('P' . $row . ':P' . ($row + $row_denda - 1));

                $sheet->mergeCells('Q' . $row . ':Q' . ($row + $row_denda - 1));

                //R,S,T,U
                if ($row_bayar == 0) {
                    $sheet->mergeCells('R' . $row . ':R' . ($row + $row_denda - 1));
                    $sheet->mergeCells('S' . $row . ':S' . ($row + $row_denda - 1));
                    $sheet->mergeCells('T' . $row . ':T' . ($row + $row_denda - 1));
                } else {
                    $sheet->mergeCells('R' . ($row + $row_bayar - 1) . ':R' . ($row + $row_denda - 1));
                    $sheet->mergeCells('S' . ($row + $row_bayar - 1) . ':S' . ($row + $row_denda - 1));
                    $sheet->mergeCells('T' . ($row + $row_bayar - 1) . ':T' . ($row + $row_denda - 1));
                }
                $sheet->mergeCells('U' . $row . ':U' . ($row + $row_denda - 1));

                $sheet->mergeCells('V' . $row . ':V' . ($row + $row_denda - 1));
                $sheet->mergeCells('W' . $row . ':W' . ($row + $row_denda - 1));

                $row += $row_denda;
            }

            if (
                $row_beasiswa > $row_tagihan &&
                $row_beasiswa > $row_denda &&
                $row_beasiswa > $row_potongan &&
                $row_beasiswa > $row_bayar
            ) {
                $sheet->mergeCells('A' . $row . ':A' . ($row + $row_beasiswa - 1));
                $sheet->mergeCells('B' . $row . ':B' . ($row + $row_beasiswa - 1));
                $sheet->mergeCells('C' . $row . ':C' . ($row + $row_beasiswa - 1));
                $sheet->mergeCells('D' . $row . ':D' . ($row + $row_beasiswa - 1));
                $sheet->mergeCells('M' . $row . ':M' . ($row + $row_beasiswa - 1));

                //E,F,G TAGIHAN
                if ($row_tagihan == 0) {
                    $sheet->mergeCells('E' . $row . ':E' . ($row + $row_beasiswa - 1));
                    $sheet->mergeCells('F' . $row . ':F' . ($row + $row_beasiswa - 1));
                } else {
                    $sheet->mergeCells('E' . ($row + $row_tagihan - 1) . ':E' . ($row + $row_beasiswa - 1));
                    $sheet->mergeCells('F' . ($row + $row_tagihan - 1) . ':F' . ($row + $row_beasiswa - 1));
                }
                $sheet->mergeCells('G' . $row . ':G' . ($row + $row_beasiswa - 1));
                //H,I,J DENDA
                if ($row_denda == 0) {
                    $sheet->mergeCells('H' . $row . ':H' . ($row + $row_beasiswa - 1));
                    $sheet->mergeCells('I' . $row . ':I' . ($row + $row_beasiswa - 1));
                } else {
                    $sheet->mergeCells('H' . ($row + $row_denda - 1) . ':H' . ($row + $row_beasiswa - 1));
                    $sheet->mergeCells('I' . ($row + $row_denda - 1) . ':I' . ($row + $row_beasiswa - 1));
                }
                $sheet->mergeCells('J' . $row . ':J' . ($row + $row_beasiswa - 1));
                //N,O,P POTONGAN
                if ($row_potongan == 0) {
                    $sheet->mergeCells('N' . $row . ':N' . ($row + $row_beasiswa - 1));
                    $sheet->mergeCells('O' . $row . ':O' . ($row + $row_beasiswa - 1));
                } else {
                    $sheet->mergeCells('N' . ($row + $row_potongan - 1) . ':N' . ($row + $row_beasiswa - 1));
                    $sheet->mergeCells('O' . ($row + $row_potongan - 1) . ':O' . ($row + $row_beasiswa - 1));
                }
                $sheet->mergeCells('P' . $row . ':P' . ($row + $row_beasiswa - 1));

                $sheet->mergeCells('Q' . $row . ':Q' . ($row + $row_beasiswa - 1));

                //R,S,T,U
                if ($row_bayar == 0) {
                    $sheet->mergeCells('R' . $row . ':R' . ($row + $row_beasiswa - 1));
                    $sheet->mergeCells('S' . $row . ':S' . ($row + $row_beasiswa - 1));
                    $sheet->mergeCells('T' . $row . ':T' . ($row + $row_beasiswa - 1));
                } else {
                    $sheet->mergeCells('R' . ($row + $row_bayar - 1) . ':R' . ($row + $row_beasiswa - 1));
                    $sheet->mergeCells('S' . ($row + $row_bayar - 1) . ':S' . ($row + $row_beasiswa - 1));
                    $sheet->mergeCells('T' . ($row + $row_bayar - 1) . ':T' . ($row + $row_beasiswa - 1));
                }
                $sheet->mergeCells('U' . $row . ':U' . ($row + $row_beasiswa - 1));

                $sheet->mergeCells('V' . $row . ':V' . ($row + $row_beasiswa - 1));
                $sheet->mergeCells('W' . $row . ':W' . ($row + $row_beasiswa - 1));

                $row += $row_beasiswa;
            }

            if (
                $row_potongan > $row_tagihan &&
                $row_potongan > $row_denda &&
                $row_potongan > $row_beasiswa &&
                $row_potongan > $row_bayar
            ) {
                $sheet->mergeCells('A' . $row . ':A' . ($row + $row_potongan - 1));
                $sheet->mergeCells('B' . $row . ':B' . ($row + $row_potongan - 1));
                $sheet->mergeCells('C' . $row . ':C' . ($row + $row_potongan - 1));
                $sheet->mergeCells('D' . $row . ':D' . ($row + $row_potongan - 1));
                $sheet->mergeCells('P' . $row . ':P' . ($row + $row_potongan - 1));

                //E,F,G TAGIHAN
                if ($row_tagihan == 0) {
                    $sheet->mergeCells('E' . $row . ':E' . ($row + $row_potongan - 1));
                    $sheet->mergeCells('F' . $row . ':F' . ($row + $row_potongan - 1));
                } else {
                    $sheet->mergeCells('E' . ($row + $row_tagihan - 1) . ':E' . ($row + $row_potongan - 1));
                    $sheet->mergeCells('F' . ($row + $row_tagihan - 1) . ':F' . ($row + $row_potongan - 1));
                }
                $sheet->mergeCells('G' . $row . ':G' . ($row + $row_potongan - 1));
                //H,I,J DENDA
                if ($row_denda == 0) {
                    $sheet->mergeCells('H' . $row . ':H' . ($row + $row_potongan - 1));
                    $sheet->mergeCells('I' . $row . ':I' . ($row + $row_potongan - 1));
                } else {
                    $sheet->mergeCells('H' . ($row + $row_denda - 1) . ':H' . ($row + $row_potongan - 1));
                    $sheet->mergeCells('I' . ($row + $row_denda - 1) . ':I' . ($row + $row_potongan - 1));
                }
                $sheet->mergeCells('J' . $row . ':J' . ($row + $row_potongan - 1));
                //K,L,M BEASISWA
                if ($row_beasiswa == 0) {
                    $sheet->mergeCells('K' . $row . ':K' . ($row + $row_potongan - 1));
                    $sheet->mergeCells('L' . $row . ':L' . ($row + $row_potongan - 1));
                } else {
                    $sheet->mergeCells('K' . ($row + $row_beasiswa - 1) . ':K' . ($row + $row_potongan - 1));
                    $sheet->mergeCells('L' . ($row + $row_beasiswa - 1) . ':L' . ($row + $row_potongan - 1));
                }
                $sheet->mergeCells('M' . $row . ':M' . ($row + $row_potongan - 1));

                $sheet->mergeCells('Q' . $row . ':Q' . ($row + $row_potongan - 1));

                //R,S,T,U
                if ($row_bayar == 0) {
                    $sheet->mergeCells('R' . $row . ':R' . ($row + $row_potongan - 1));
                    $sheet->mergeCells('S' . $row . ':S' . ($row + $row_potongan - 1));
                    $sheet->mergeCells('T' . $row . ':T' . ($row + $row_potongan - 1));
                } else {
                    $sheet->mergeCells('R' . ($row + $row_bayar - 1) . ':R' . ($row + $row_potongan - 1));
                    $sheet->mergeCells('S' . ($row + $row_bayar - 1) . ':S' . ($row + $row_potongan - 1));
                    $sheet->mergeCells('T' . ($row + $row_bayar - 1) . ':T' . ($row + $row_potongan - 1));
                }
                $sheet->mergeCells('U' . $row . ':U' . ($row + $row_potongan - 1));

                $sheet->mergeCells('V' . $row . ':V' . ($row + $row_potongan - 1));
                $sheet->mergeCells('W' . $row . ':W' . ($row + $row_potongan - 1));

                $row += $row_potongan;
            }

            if (
                $row_bayar > $row_tagihan &&
                $row_bayar > $row_denda &&
                $row_bayar > $row_beasiswa &&
                $row_bayar > $row_potongan
            ) {
                $sheet->mergeCells('A' . $row . ':A' . ($row + $row_bayar - 1));
                $sheet->mergeCells('B' . $row . ':B' . ($row + $row_bayar - 1));
                $sheet->mergeCells('C' . $row . ':C' . ($row + $row_bayar - 1));
                $sheet->mergeCells('D' . $row . ':D' . ($row + $row_bayar - 1));
                $sheet->mergeCells('U' . $row . ':U' . ($row + $row_bayar - 1));

                //E,F,G TAGIHAN
                if ($row_tagihan == 0) {
                    $sheet->mergeCells('E' . $row . ':E' . ($row + $row_bayar - 1));
                    $sheet->mergeCells('F' . $row . ':F' . ($row + $row_bayar - 1));
                } else {
                    $sheet->mergeCells('E' . ($row + $row_tagihan - 1) . ':E' . ($row + $row_bayar - 1));
                    $sheet->mergeCells('F' . ($row + $row_tagihan - 1) . ':F' . ($row + $row_bayar - 1));
                }
                $sheet->mergeCells('G' . $row . ':G' . ($row + $row_bayar - 1));
                //H,I,J DENDA
                if ($row_denda == 0) {
                    $sheet->mergeCells('H' . $row . ':H' . ($row + $row_bayar - 1));
                    $sheet->mergeCells('I' . $row . ':I' . ($row + $row_bayar - 1));
                } else {
                    $sheet->mergeCells('H' . ($row + $row_denda - 1) . ':H' . ($row + $row_bayar - 1));
                    $sheet->mergeCells('I' . ($row + $row_denda - 1) . ':I' . ($row + $row_bayar - 1));
                }
                $sheet->mergeCells('J' . $row . ':J' . ($row + $row_bayar - 1));
                //K,L,M BEASISWA
                if ($row_beasiswa == 0) {
                    $sheet->mergeCells('K' . $row . ':K' . ($row + $row_bayar - 1));
                    $sheet->mergeCells('L' . $row . ':L' . ($row + $row_bayar - 1));
                } else {
                    $sheet->mergeCells('K' . ($row + $row_beasiswa - 1) . ':K' . ($row + $row_bayar - 1));
                    $sheet->mergeCells('L' . ($row + $row_beasiswa - 1) . ':L' . ($row + $row_bayar - 1));
                }
                $sheet->mergeCells('M' . $row . ':M' . ($row + $row_bayar - 1));
                //N,O,P POTONGAN
                if ($row_potongan == 0) {
                    $sheet->mergeCells('N' . $row . ':N' . ($row + $row_bayar - 1));
                    $sheet->mergeCells('O' . $row . ':O' . ($row + $row_bayar - 1));
                } else {
                    $sheet->mergeCells('N' . ($row + $row_potongan - 1) . ':N' . ($row + $row_bayar - 1));
                    $sheet->mergeCells('O' . ($row + $row_potongan - 1) . ':O' . ($row + $row_bayar - 1));
                }
                $sheet->mergeCells('P' . $row . ':P' . ($row + $row_bayar - 1));

                $sheet->mergeCells('Q' . $row . ':Q' . ($row + $row_bayar - 1));

                $sheet->mergeCells('V' . $row . ':V' . ($row + $row_bayar - 1));
                $sheet->mergeCells('W' . $row . ':W' . ($row + $row_bayar - 1));

                $row += $row_bayar;
            }
        }

        $response = response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $nameFile = $type == 'new' ? 'Mahasiswa Baru':'Mahasiswa Lama';
        $response->headers->set('Content-Disposition', 'attachment; filename="Laporan Tagihan '.$nameFile.'.xlsx"');
        $response->send();
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

    function newStudentHistory($student_number, Request $request)
    {
        $search = $request->get('search_filter');
        $data = $this->getNew('prrb.*')->where('p.par_id', '=', $student_number)->distinct()->get();
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

    function studentRegistrant(Request $request){
        $data = Register::with('participant', 'studyProgram', 'lectureType', 'period', 'path' ,'paymentRegister', 'year');

        if($request->get('angkatan', '#ALL') !== '#ALL'){
            $data = $data->whereHas('year', function($q) use($request) {
                $q->where('msy_id', '=', $request->get('angkatan'));
            });
        }

        if($request->get('path', '#ALL') !== '#ALL'){
            $data = $data->whereHas('path', function($q) use($request) {
                $q->where('path_id', '=', $request->get('path'));
            });
        }

        if($request->get('period', '#ALL') !== '#ALL'){
            $data = $data->whereHas('period', function($q) use($request){
                $q->where('period_id', '=', $request->get('period'));
            });
        }

        return DataTables($data->get())->toJson();
    }

    function getColomns()
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

    function getNew()
    {
        $list_colomns = func_get_args();
        return DB::table('masterdata.ms_studyprogram as ms')
            ->select($list_colomns)
            ->join('pmb.register as r', 'r.reg_major_pass', '=', 'ms.studyprogram_id')
            ->join('pmb.participant as p', 'r.par_id', '=', 'p.par_id')
            ->join('masterdata.ms_school_year as msy', 'msy.msy_id', '=', 'r.ms_school_year_id')
            ->join('finance.payment_re_register as prr', 'prr.reg_id', '=', 'r.reg_id')
            ->join('finance.payment_re_register_detail as prrd', 'prrd.prr_id', '=', 'prr.prr_id')
            ->join('finance.payment_re_register_bill as prrb', 'prrb.prr_id', '=', 'prr.prr_id')
            ->whereNull('prr.deleted_at')
            ->whereNull('prrd.deleted_at')
            ->whereNull('prrb.deleted_at');
    }

    function getProdi($faculty)
    {
        $data = Studyprogram::where('faculty_id', '=', $faculty)->get();

        return $data;
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
}
