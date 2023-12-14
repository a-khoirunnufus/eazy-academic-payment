<?php

namespace App\Http\Controllers\_Payment\Api;

use Illuminate\Http\Request;
use DB;

use App\Http\Controllers\Controller;
use App\Models\Payment\Year as SchoolYear;
use App\Models\Payment\Faculty;
use App\Models\Payment\Studyprogram;
use App\Models\Payment\Period;
use App\Models\Payment\Path;
use App\Models\Payment\LectureType;
use App\Models\Payment\Scholarship;
use App\Models\Payment\Discount;
use App\Models\Payment\MasterPaymentTypeMidtrans;
use App\Models\Payment\MasterPaymentTypeFinpay;
use App\Models\Payment\MasterPaymentTypeManual;
use App\Models\Payment\MasterPaymentMethodNew;

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

    public function lectureTypeIndex()
    {
        $data = LectureType::all();
        return response()->json($data->toArray());
    }

    public function scholarshipIndex()
    {
        $scholarship = Scholarship::with(['periodStart', 'periodEnd'])->get();

        return response()->json($scholarship->toArray());
    }

    public function scholarshipShow($ms_id)
    {
        $scholarship = Scholarship::with(['periodStart', 'periodEnd'])
            ->where('ms_id', $ms_id)
            ->first();

        return response()->json($scholarship->toArray());
    }

    public function discountIndex()
    {
        $discount = Discount::with(['periodStart', 'periodEnd'])->get();

        return response()->json($discount->toArray());
    }

    public function discountShow($ms_id)
    {
        $discount = Discount::with(['periodStart', 'periodEnd'])
            ->where('ms_id', $ms_id)
            ->first();

        return response()->json($discount->toArray());
    }

    public function paymentTypeIndex(Request $request)
    {
        $payment_service = $request->query('payment_service');

        $active_only = $request->query('active_only') ?? false;
        if ($active_only) {
            if ($active_only === 'true') $active_only = true;
            if ($active_only === 'false') $active_only = false;
        }

        $data = [];

        if ($payment_service) {
            if ($payment_service == 'midtrans') {
                $data = $active_only ? MasterPaymentTypeMidtrans::active()->get()->toArray()
                    : MasterPaymentTypeMidtrans::get()->toArray();
            }
            if ($payment_service == 'finpay') {
                $data = $active_only ? MasterPaymentTypeFinpay::active()->get()->toArray()
                    : MasterPaymentTypeFinpay::get()->toArray();
            }
            if ($payment_service == 'manual') {
                $data = $active_only ? MasterPaymentTypeManual::active()->get()->toArray()
                    : MasterPaymentTypeManual::get()->toArray();
            }
        }
        else {
            $midtrans_active = DB::table('finance.ms_settings')->where('name', 'payment_with_midtrans_active')->first()->value == '1';
            $finpay_active = DB::table('finance.ms_settings')->where('name', 'payment_with_finpay_active')->first()->value == '1';
            $manual_active = DB::table('finance.ms_settings')->where('name', 'payment_with_manual_active')->first()->value == '1';

            if ($midtrans_active) {
                $data[] = [
                    'payment_service' => 'midtrans',
                    'data' => $active_only ? MasterPaymentTypeMidtrans::active()->get()->toArray()
                        : MasterPaymentTypeMidtrans::get()->toArray(),
                ];
            }

            if ($finpay_active) {
                $data[] = [
                    'payment_service' => 'finpay',
                    'data' => $active_only ? MasterPaymentTypeFinpay::active()->get()->toArray()
                        : MasterPaymentTypeFinpay::get()->toArray(),
                ];
            }

            if ($manual_active) {
                $data[] = [
                    'payment_service' => 'manual',
                    'data' => $active_only ? MasterPaymentTypeManual::active()->get()->toArray()
                        : MasterPaymentTypeManual::get()->toArray(),
                ];
            }
        }

        return response()->json($data);
    }

    public function paymentTypeShow($payment_service, $code)
    {
        $data = null;

        if ($payment_service == 'midtrans') {
            $data = MasterPaymentTypeMidtrans::find($code);
        }

        if ($payment_service == 'finpay') {
            $data = MasterPaymentTypeFinpay::find($code);
        }

        if ($payment_service == 'manual') {
            $data = MasterPaymentTypeManual::find($code);
        }

        if (is_null($data)) {
            return response()->json(['message'=> 'payment type not found!'], 404);
        }

        return response()->json($data->toArray());
    }

    public function paymentMethodIndex()
    {
        $payment_method = MasterPaymentMethodNew::all();

        return response()->json($payment_method->toArray());
    }

    public function paymentMethodShow($code)
    {
        $payment_method = MasterPaymentMethodNew::find($code);

        return response()->json($payment_method->toArray());
    }

    public function masterSettingIndex()
    {
        $settings = DB::table('finance.ms_settings')->get();

        return response()->json($setting->toArray());
    }

    public function masterSettingShow($name)
    {
        $setting = DB::table('finance.ms_settings')
            ->where('name', $name)
            ->first();

        if (is_null($setting)) {
            return response()->json(['message' => 'setting not found!'], 404);
        }

        return response()->json(['value' => $setting->value]);
    }
}
