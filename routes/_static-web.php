<?php

use App\Http\Controllers\_Payment\AcademicRules;
use App\Http\Controllers\_Payment\SettingsController;

Route::get('/setting/invoice-component', fn() => view('pages.setting.invoice-component'));
Route::get('/setting/instalment-template', fn() => view('pages.setting.instalment-template'));
Route::get('/setting/rates', fn() => view('pages.setting.rates'));
Route::get('/setting/rates-per-course', fn() => view('pages.setting.rates-per-course'));
Route::get('/setting/registration-form', [SettingsController::class, "registrationForm"]);
Route::get('/setting/academic-rules', [AcademicRules::class, "index"]);

Route::get('/generate/old-student-invoice', fn() => view('pages.generate.old-student-invoice'));
Route::get('/generate/new-student-invoice', fn() => view('pages.generate.new-student-invoice'));
Route::get('/generate/student-invoice-detail', fn() => view('pages.generate.student-invoice-detail'));
Route::get('/generate/other-invoice', fn() => view('pages.generate.other-invoice'));
Route::get('/generate/other-invoice-detail', fn() => view('pages.generate.other-invoice-detail'));

Route::get('/report/old-student-invoice', function(Request $request) {
    if ($request->query('type') == 'student') {
        return view('pages.report.old-student-invoice.per-student');
    } else {
        return view('pages.report.old-student-invoice.per-study-program');
    }
});
Route::get('/report/new-student-invoice', function(Request $request) {
    if ($request->query('type') == 'student') {
        return view('pages.report.new-student-invoice.per-student');
    } else {
        return view('pages.report.new-student-invoice.per-study-program');
    }
});
Route::get('/report/registrant-invoice', fn() => view('pages.report.registrant-invoice'));
Route::get('/report/old-student-receivables', function(Request $request) {
    if ($request->query('type') == 'student') {
        return view('pages.report.old-student-receivables.per-student');
    } else {
        return view('pages.report.old-student-receivables.per-study-program');
    }
});
Route::get('/report/new-student-receivables', function(Request $request) {
    if ($request->query('type') == 'student') {
        return view('pages.report.new-student-receivables.per-student');
    } else {
        return view('pages.report.new-student-receivables.per-study-program');
    }
});
