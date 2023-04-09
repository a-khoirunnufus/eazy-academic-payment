<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Auth::routes();

Route::get('/', fn() => redirect('/login'));

Route::group(['middleware' => ['auth', 'access']], function(){

    Route::get('/studyprogram', fn() => view('pages.studyprogram.index'));
    Route::get('/curriculum', fn() => view('pages.curriculum.index'));

    Route::get('/subjects', fn() => '-');
    Route::get('/learning-methods', fn() => '-');

    // Route::group([
    //     'prefix' => 'subjects', 
    //     'middleware' => ['can:access_']
    // ], function(){
    //     Route::
    // });

});

Route::get('/setting/invoice-component', fn() => view('pages.setting.invoice-component'));
Route::get('/setting/instalment-template', fn() => view('pages.setting.instalment-template'));
Route::get('/setting/rates', fn() => view('pages.setting.rates'));
Route::get('/setting/rates-per-course', fn() => view('pages.setting.rates-per-course'));
Route::get('/setting/registration-form', fn() => view('pages.setting.registration-form'));
Route::get('/setting/academic-rules', fn() => view('pages.setting.academic-rules'));

Route::get('/generate/registrant-invoice', fn() => view('pages.generate.registrant-invoice'));
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