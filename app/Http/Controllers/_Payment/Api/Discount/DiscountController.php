<?php

namespace App\Http\Controllers\_Payment\API\Discount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Payment\Discount\DiscountRequest;
use App\Models\Payment\Discount;
use App\Models\Payment\Year;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DiscountController extends Controller
{

    public function index(Request $request)
    {
        $filters = $request->input('custom_filters');
        $filters = array_filter($filters, function ($item) {
            return !is_null($item) && $item != '#ALL';
        });

        $query = Discount::query();
        $query = $query->with('periodStart','periodEnd');

        if (isset($filters['md_period_start_filter'])) {
            $query = $query->where('md_period_start', '=', $filters['md_period_start_filter']);
        }

        if (isset($filters['md_period_end_filter'])) {
            $query = $query->where('md_period_end', '=', $filters['md_period_end_filter']);
        }

        if (isset($filters['status_filter'])){
            $query = $query->where("md_status", "=", $filters['status_filter']);
        }

        $query = $query->orderBy('md_id')->get();

        if(isset($filters['search_filter'])){
            // echo "pencarian";
            $data = [];
            foreach($query as $item){
                $isFound = false;

                if(strpos(strtolower($item->md_name), strtolower($filters['search_filter'])) !== false){
                    if(!$isFound){
                        $isFound = true;
                        array_push($data, $item);
                        // echo "found: ".strtolower($item->md_name).'<br>';
                    }
                }

                if(strpos(strtolower($item->periodStart->msy_year).' '.strtolower($item->periodStart->msy_semester == 1 ? 'Ganjil' : 'Genap'), strtolower($filters['search_filter'])) !== false){
                    if(!$isFound){
                        $isFound = true;
                        array_push($data, $item);
                        // echo "found: ".strtolower($item->periodStart->msy_year).' '.strtolower($item->periodStart->msy_semester == 1 ? 'Ganjil' : 'Genap').'<br>';
                    }
                }

                if(strpos(strtolower($item->periodEnd->msy_year).' '.strtolower($item->periodEnd->msy_semester == 1 ? 'Ganjil' : 'Genap'), strtolower($filters['search_filter'])) !== false){
                    if(!$isFound){
                        $isFound = true;
                        array_push($data, $item);
                        // echo "found: ".strtolower($item->periodEnd->msy_year).' '.strtolower($item->periodEnd->msy_semester == 1 ? 'Ganjil' : 'Genap').'<br>';
                    }
                }

                if(strpos(strtolower('Rp'.$item->md_nominal), strtolower($filters['search_filter'])) !== false){
                    if(!$isFound){
                        $isFound = true;
                        array_push($data, $item);
                        // echo "found: ".strtolower('Rp'.$item->md_nominal).'<br>';
                    }
                }

                if(strpos(strtolower('Rp'.$item->md_budget), strtolower($filters['search_filter'])) !== false){
                    if(!$isFound){
                        $isFound = true;
                        array_push($data, $item);
                        // echo "found: ".strtolower('Rp'.$item->md_budget).'<br>';
                    }
                }

                if(strpos(strtolower('Rp'.$item->md_realization), strtolower($filters['search_filter'])) !== false){
                    if(!$isFound){
                        $isFound = true;
                        array_push($data, $item);
                        // echo "found: ".strtolower('Rp'.$item->md_realization).'<br>';
                    }
                }

                if(strpos(strtolower($item->md_status == 1 ? 'Aktif':'Tidak Aktif'), strtolower($filters['search_filter'])) !== false){
                    if(!$isFound){
                        $isFound = true;
                        array_push($data, $item);
                        // echo "found: ".strtolower($item->md_status == 1 ? 'Aktif':'Tidak Aktif').'<br>';
                    }
                }
            }
            // return "";
            return datatables($data)->toJson();
        }

        return datatables($query)->toJson();
    }

    public function period()
    {
        $query = Year::all();
        return $query;
    }

    public function store(DiscountRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try{
            if(array_key_exists("msc_id",$validated)){
                $data = Discount::findOrFail($validated["msc_id"]);
                $data->update($validated);
                $text = "Berhasil memperbarui potongan";
            }else{
                Discount::create($validated + [
                    'md_realization' => 0
                ]);
                $text = "Berhasil menambahkan potongan";
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return response()->json($e->getMessage());
        }
        return json_encode(array('success' => true, 'message' => $text));
    }

    public function delete($id)
    {
        $data = Discount::findOrFail($id);
        $data->delete();

        return json_encode(array('success' => true, 'message' => "Berhasil menghapus potongan"));
    }

    public function exportData(Request $request)
    {
        $textData = $request->post('data');
        $data = json_decode($textData);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //header table
        $sheet->setCellValue('A1', 'Nama Potongan');
        $sheet->setCellValue('B1', 'Periode Awal');
        $sheet->setCellValue('C1', 'Periode Akhir');
        $sheet->setCellValue('D1', 'Nominal');
        $sheet->setCellValue('E1', 'Anggaran');
        $sheet->setCellValue('F1', 'Realisasi');
        $sheet->setCellValue('G1', 'Status');

        //content
        $row = 2;
        foreach($data as $item){
            $sheet->setCellValue('A'.$row, $item->md_name);
            $sheet->setCellValue('B'.$row, $item->period_start->msy_year.' '.($item->period_start->msy_semester == 1 ? 'Ganjil':'Genap'));
            $sheet->setCellValue('C'.$row, $item->period_end->msy_year.' '.($item->period_end->msy_semester == 1 ? 'Ganjil':'Genap'));
            $sheet->setCellValue('D'.$row, $item->md_nominal);
            $sheet->setCellValue('E'.$row, $item->md_budget);
            $sheet->setCellValue('F'.$row, $item->md_realization);
            $sheet->setCellValue('G'.$row, $item->md_status == 1 ? "Aktif":"Tidak Aktif");
            $row++;
        }

        $response = response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="Laporan Program Potongan.xlsx"');
        $response->send();
    }

    public function show($md_id)
    {
        $discount = Discount::with(['periodStart', 'periodEnd'])
            ->where('md_id', $md_id)
            ->first();

        return response()->json($discount->toArray());
    }
}
