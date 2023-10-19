<?php

namespace App\Http\Controllers\Api\Data;

use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolYear\StoreRequest;
use App\Models\SchoolYear;
use Illuminate\Http\Request;
use App\Services\SchoolYearService;

class SchoolYearController extends Controller
{
    public function index()
    {
        $query = SchoolYear::orderBy('msy_end_date', 'desc');

        $datatable = datatables($query);

        return $datatable->toJSON();
    }

    public function store(StoreRequest $request)
    {
        SchoolYear::create($request->validated());
        
        SchoolYearService::updateCachedActiveData();

        return response()->json(['success' => true]);
    }

    public function update(StoreRequest $request, $id)
    {
        $schoolYear = SchoolYear::findOrFail($id);
        $schoolYear->update($request->validated());

        SchoolYearService::updateCachedActiveData();

        return response()->json(['success' => true]); 
    }

    /**
     * This method will accessed with GET Method
     * There is several mode to get active data
     * # Default is on current date
     * # With Query Params:
     * - 'date' (on selected date)
     * - 'start_date' and 'end_date' (With date range)
     */
    public function getActiveData(Request $request)
    {
        $activeData = null;

        if ($request->get('date')) {
            $activeData = SchoolYearService::getActiveByDate($request->get('date'));
        } else if ($request->get('start_date') && $request->get('end_date')) {
            $activeData = SchoolYearService::getActiveByDateRange($request->get('start_date'), $request->get('end_date'));
        } else {
            $activeData = SchoolYearService::getActiveByDate();
        }

        return response()->json([
            'exists' => $activeData != null ? true : false,
            'data' => $activeData
        ]);
    }

    public function getAllData()
    {
        $data = SchoolYear::query()->orderBy('msy_code', 'desc')->get();
        return response()->json($data);
    }
}
