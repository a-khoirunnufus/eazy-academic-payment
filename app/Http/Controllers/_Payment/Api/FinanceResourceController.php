<?php

namespace App\Http\Controllers\_Payment\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\Year as SchoolYear;
use App\Models\Payment\Faculty;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\Period;
use App\Models\Payment\Path;

class FinanceResourceController extends Controller
{
    public function schoolYearIndex(Request $request)
    {
        $semester = $request->get('semester');

        $query = SchoolYear::orderBy('msy_code');

        if ($semester)
            $query->where('msy_semester', $semester);

        return response()->json($query->get()->toArray());
    }

    public function schoolYearShow($msy_id)
    {
        $data = SchoolYear::find($msy_id)->toArray();

        return response()->json($data);
    }

    public function FacultyIndex()
    {
        $data = Faculty::orderBy('faculty_name')->get()->toArray();

        return response()->json($data);
    }

    public function FacultyShow($faculty_id)
    {
        $data = Faculty::find($faculty_id)->toArray();

        return response()->json($data);
    }

    public function StudyprogramIndex(Request $request)
    {
        $query = Studyprogram::query();

        if ($request->has('faculty') && $request->get('faculty') != '') {
            $query = $query->where('faculty_id', $request->get('faculty'));
        }

        $data = $query->orderBy('studyprogram_type', 'asc')
            ->orderBy('studyprogram_name', 'asc')
            ->get()
            ->toArray();

        return response()->json($data);
    }

    public function StudyprogramShow($studyprogram_id)
    {
        $data = Studyprogram::find($studyprogram_id)->toArray();

        return response()->json($data);
    }

    public function registrationPeriodIndex()
    {
        $data = Period::orderBy('period_start')->get();

        return response()->json($data->toArray());
    }

    public function registrationPeriodShow($period_id)
    {
        $data = Period::find($period_id);

        return response()->json($data->toArray());
    }

    public function registrationPathIndex()
    {
        $data = Path::orderBy('path_name')->get();

        return response()->json($data->toArray());
    }

    public function registrationPathShow($path_id)
    {
        $data = Path::find($path_id);

        return response()->json($data->toArray());
    }
}
