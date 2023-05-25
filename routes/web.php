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

// Static Routes
include __DIR__.DIRECTORY_SEPARATOR.'_static-web.php';

// Payment
Route::group(['prefix' => 'payment'], function(){
    // Settings
    Route::group(['prefix' => 'settings'], function(){
        // Component Invoices
        Route::get('component', 'App\Http\Controllers\_Payment\SettingsController@component')->name('payment.settings.component');
        Route::get('payment-rates', 'App\Http\Controllers\_Payment\SettingsController@paymentrates')->name('payment.settings.payment-rates');
        Route::get('payment-rates/detail/{id}', 'App\Http\Controllers\_Payment\SettingsController@paymentratesdetail')->name('payment.settings.payment-rates.detail');
        Route::get('subject-rates', 'App\Http\Controllers\_Payment\SettingsController@subjectrates')->name('payment.settings.subject-rates');
        Route::get('credit-schema', 'App\Http\Controllers\_Payment\SettingsController@creditSchema')->name('payment.settings.credit-schema');
    });

    // Generate
    Route::group(['prefix' => 'generate'], function(){
        Route::get('new-student-invoice/per-institution', 'App\Http\Controllers\_Payment\Frontend\Generate\NewStudentInvoiceController@perInstitution')->name('payment.generate.new-student-invoice.per-institution');
        Route::get('new-student-invoice/per-faculty', 'App\Http\Controllers\_Payment\Frontend\Generate\NewStudentInvoiceController@perFaculty')->name('payment.generate.new-student-invoice.per-faculty');
        Route::get('new-student-invoice/per-studyprogram', 'App\Http\Controllers\_Payment\Frontend\Generate\NewStudentInvoiceController@perStudyprogram')->name('payment.generate.new-student-invoice.per-studyprogram');
        Route::get('new-student-invoice/per-student', 'App\Http\Controllers\_Payment\Frontend\Generate\NewStudentInvoiceController@perStudent')->name('payment.generate.new-student-invoice.per-student');

        Route::get('student-invoice', 'App\Http\Controllers\_Payment\GenerateController@StudentInvoice')->name('payment.generate.student-invoice');
        Route::get('student-invoice/detail', 'App\Http\Controllers\_Payment\GenerateController@StudentInvoiceDetail')->name('payment.generate.student-invoice-detail');
    });
});

Route::get('test', function() {
    return view('test');
});
