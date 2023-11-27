<?php

namespace App\Http\Controllers\_Payment\API\Scholarship;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\Scholarship;
use App\Http\Requests\Payment\Scholarship\ScholarshipRequest;
use App\Models\Payment\Year;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ScholarshipController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->input('custom_filters');
        $filters = array_filter($filters, function ($item) {
            return !is_null($item) && $item != '#ALL';
        });

        $query = Scholarship::query();
        $query = $query->with('periodStart', 'periodEnd');

        if (isset($filters['ms_period_start_filter'])) {
            $query = $query->where('ms_period_start', '=', $filters['ms_period_start_filter']);
        }

        if (isset($filters['ms_period_end_filter'])) {
            $query = $query->where('ms_period_end', '=', $filters['ms_period_end_filter']);
        }

        if (isset($filters['status_filter'])) {
            $query = $query->where('ms_status', '=', $filters['status_filter']);
        }

        if (isset($filters['type_filter'])) {
            $query = $query->where('ms_type', '=', $filters['type_filter']);
        }

        $query = $query->orderBy('ms_id')->get();

        if (isset($filters['search_filter'])) {
            $data = [];
            foreach ($query as $item) {
                $isFound = false;
                // if (strpos(strtolower(json_encode($item)), strtolower($filters['search_filter']))) {
                //     array_push($data, $item);
                // }
                if (strpos(strtolower($item->ms_name), strtolower($filters['search_filter'])) !== false) {
                    if (!$isFound) {
                        $isFound = true;
                        array_push($data, $item);
                    }
                }

                if (strpos(strtolower($item->ms_type == '1' ? "Internal" : "External"), strtolower($filters['search_filter'])) !== false) {
                    if (!$isFound) {
                        $isFound = true;
                        array_push($data, $item);
                    }
                }

                if (strpos(strtolower($item->ms_status == '1' ? "Aktif" : "Tidak Aktif"), strtolower($filters['search_filter'])) !== false) {
                    if (!$isFound) {
                        $isFound = true;
                        array_push($data, $item);
                    }
                }

                if (strpos(strtolower($item->ms_from), strtolower($filters['search_filter'])) !== false) {
                    if (!$isFound) {
                        $isFound = true;
                        array_push($data, $item);
                    }
                }

                if (strpos(strtolower($item->ms_from_name), strtolower($filters['search_filter'])) !== false) {
                    if (!$isFound) {
                        $isFound = true;
                        array_push($data, $item);
                    }
                }

                if (strpos(strtolower(($item->periodStart->msy_year) . ' ' . ($item->periodStart->msy_semester == '1' ? "Ganjil" : "Genap")), strtolower($filters['search_filter'])) !== false) {
                    if (!$isFound) {
                        $isFound = true;
                        array_push($data, $item);
                    }
                }

                if (strpos(strtolower($item->periodEnd->msy_year . ' ' . ($item->periodEnd->msy_semester == '1' ? "Ganjil" : "Genap")), strtolower($filters['search_filter'])) !== false) {
                    if (!$isFound) {
                        $isFound = true;
                        array_push($data, $item);
                    }
                }

                if (strpos(strtolower('Rp' . $item->ms_nominal), strtolower($filters['search_filter'])) !== false) {
                    if (!$isFound) {
                        $isFound = true;
                        array_push($data, $item);
                    }
                }

                if (strpos(strtolower('Rp' . $item->ms_budget), strtolower($filters['search_filter'])) !== false) {
                    if (!$isFound) {
                        $isFound = true;
                        array_push($data, $item);
                    }
                }

                if (strpos(strtolower('Rp' . $item->ms_realization), strtolower($filters['search_filter'])) !== false) {
                    if (!$isFound) {
                        $isFound = true;
                        array_push($data, $item);
                    }
                }
            }
            return datatables($data)->toJson();
        }

        return datatables($query)->toJson();
    }

    public function period()
    {
        $query = Year::all();
        return $query;
    }

    public function store(ScholarshipRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            if (array_key_exists("msc_id", $validated)) {
                $data = Scholarship::findOrFail($validated["msc_id"]);
                $data->update($validated);
                $text = "Berhasil memperbarui beasiswa";
            } else {
                Scholarship::create($validated + [
                    'ms_realization' => 0
                ]);
                $text = "Berhasil menambahkan beasiswa";
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function delete($id)
    {
        $data = Scholarship::findOrFail($id);
        $data->delete();

        return json_encode(array('success' => true, 'message' => "Berhasil menghapus beasiswa"));
    }

    public function exportData(Request $request)
    {
        $textData = $request->post('data');
        $data = json_decode($textData);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //header table
        $sheet->setCellValue('A1', 'Nama Beasiswa');
        $sheet->setCellValue('B1', 'Jenis');
        $sheet->setCellValue('C1', 'Instansi/Perusahaan');
        $sheet->setCellValue('D1', 'PIC');
        $sheet->setCellValue('E1', 'Kontak');
        $sheet->setCellValue('F1', 'Periode Mulai');
        $sheet->setCellValue('G1', 'Periode Berakhir');
        $sheet->setCellValue('H1', 'Nominal');
        $sheet->setCellValue('I1', 'Anggaran');
        $sheet->setCellValue('J1', 'Realisasi');
        $sheet->setCellValue('K1', 'Status');

        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->ms_name);
            $sheet->setCellValue('B' . $row, $item->ms_type == 1 ? "Internal":"External");
            $sheet->setCellValue('C' . $row, $item->ms_from);
            $sheet->setCellValue('D' . $row, $item->ms_from_name);
            $sheet->setCellValue('E' . $row, $item->ms_from_phone);
            $sheet->setCellValue('F' . $row, $item->period_start->msy_year." ".($item->period_start->msy_semester == 1 ? 'Ganjil' : 'Genap'));
            $sheet->setCellValue('G' . $row, $item->period_end->msy_year." ".($item->period_end->msy_semester == 1 ? 'Ganjil' : 'Genap'));
            $sheet->setCellValue('H' . $row, $item->ms_nominal);
            $sheet->setCellValue('I' . $row, $item->ms_budget);
            $sheet->setCellValue('J' . $row, $item->ms_realization);
            $sheet->setCellValue('K' . $row, $item->ms_status == 1 ? "Aktif":"Tidak Aktif");

            $row++;
        }

        foreach(range('A', 'K') as $colId){
            $sheet->getColumnDimension($colId)->setAutoSize(true);
        }

        $response = response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Laporan Mahasiswa Penerima Beasiswa.xlsx"');
        $response->send();
    }
}
