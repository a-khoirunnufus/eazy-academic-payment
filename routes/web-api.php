<?php

use Illuminate\Http\Request;

Route::group(['middleware' => ['auth']], function(){

    Route::resource('curriculum', 'App\Http\Controllers\Api\CurriculumController');
    Route::resource('studyprogram', 'App\Http\Controllers\Api\StudyprogramController');

    // File Resource (if you want move this, make sure update public/plugins/filepond.js)
    Route::post('resources/upload', 'App\Http\Controllers\Api\ResourceController@upload');
    Route::delete('resources/{id}', 'App\Http\Controllers\Api\ResourceController@destroy');
});

// Static Routes
include __DIR__.DIRECTORY_SEPARATOR.'_static-web-api.php';

// Payment
Route::group(['prefix' => 'payment'], function(){
    // Settings
    Route::group(['prefix' => 'settings'], function(){
        // Component Invoices
        Route::get('component/index', 'App\Http\Controllers\_Payment\Api\Settings\ComponentInvoiceController@index');
        Route::get('component-type', 'App\Http\Controllers\_Payment\Api\Settings\ComponentInvoiceController@getComponentType');
        Route::post('component/store', 'App\Http\Controllers\_Payment\Api\Settings\ComponentInvoiceController@store');
        Route::delete('component/delete/{id}', 'App\Http\Controllers\_Payment\Api\Settings\ComponentInvoiceController@delete');
        Route::post('component/upload-file-for-import', 'App\Http\Controllers\_Payment\Api\Settings\ComponentInvoiceController@uploadFileForImport');
        Route::get('component/dt-import-preview', 'App\Http\Controllers\_Payment\Api\Settings\ComponentInvoiceController@dtImportPreview');
        Route::post('component/import', 'App\Http\Controllers\_Payment\Api\Settings\ComponentInvoiceController@import');

        Route::get('courserates/index', 'App\Http\Controllers\_Payment\Api\Settings\CourseRatesController@index');
        Route::get('courserates/template', 'App\Http\Controllers\_Payment\Api\Settings\CourseRatesController@template');
        Route::get('courserates/studyprogram/{id?}', 'App\Http\Controllers\_Payment\Api\Settings\CourseRatesController@getStudyProgram');
        Route::get('courserates/course/{id}', 'App\Http\Controllers\_Payment\Api\Settings\CourseRatesController@getMataKuliah');
        Route::get('courserates/getbycourseid/{id}', 'App\Http\Controllers\_Payment\Api\Settings\CourseRatesController@getCourseRateByCourseId');
        Route::post('courserates/store', 'App\Http\Controllers\_Payment\Api\Settings\CourseRatesController@store');
        Route::post('courserates/import', 'App\Http\Controllers\_Payment\Api\Settings\CourseRatesController@import');
        Route::post('courserates/preview', 'App\Http\Controllers\_Payment\Api\Settings\CourseRatesController@preview');

        Route::get('paymentrates/index', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@index');
        Route::get('paymentrates/getrowdata/{id}', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@getRowData');
        Route::get('paymentrates/detail/{id}', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@detail');
        Route::post('paymentrates/import/{id}', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@import');
        Route::get('paymentrates/period', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@getPeriod');
        Route::get('paymentrates/path', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@getPath');
        Route::get('paymentrates/component', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@getComponent');
        Route::get('paymentrates/schema', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@getSchema');
        Route::get('paymentrates/studyprogram', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@getStudyProgram');
        Route::get('paymentrates/lecture-type', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@getLectureType');
        Route::get('paymentrates/credit-schema', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@getCreditSchema');
        Route::get('paymentrates/getschemabyid/{ppm_id}/{cs_id}', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@getSchemaById');
        Route::get('paymentrates/removeschemabyid/{ppm_id}/{cs_id}', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@removeSchemaById');
        Route::post('paymentrates/store', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@store');
        Route::post('paymentrates/update', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@update');
        Route::delete('paymentrates/delete/{id}', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@delete');
        Route::delete('paymentrates/deletecomponent/{id}', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@deletecomponent');
        Route::get('paymentrates/download-file-for-import', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@downloadFileForImport');
        Route::post('paymentrates/upload-file-for-import', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@uploadFileForImport');
        Route::get('paymentrates/dt-import-preview', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@dtImportPreview');
        Route::post('paymentrates/import', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@importSettingFee');

        Route::get('credit-schema/index', 'App\Http\Controllers\_Payment\Api\Settings\CreditSchemaController@index');
        Route::get('credit-schema/show/{id}', 'App\Http\Controllers\_Payment\Api\Settings\CreditSchemaController@show');
        Route::post('credit-schema/store', 'App\Http\Controllers\_Payment\Api\Settings\CreditSchemaController@store');
        Route::put('credit-schema/update/{id}', 'App\Http\Controllers\_Payment\Api\Settings\CreditSchemaController@update');
        Route::delete('credit-schema/delete/{id}', 'App\Http\Controllers\_Payment\Api\Settings\CreditSchemaController@delete');
    });

    Route::group(['prefix' => 'generate'], function(){
        // New Student Invoice
        Route::get('new-student-invoice/index', 'App\Http\Controllers\_Payment\Api\Generate\NewStudentInvoiceController@index');
        Route::get('new-student-invoice/detail', 'App\Http\Controllers\_Payment\Api\Generate\NewStudentInvoiceController@detail');
        Route::get('new-student-invoice/show-invoice/{prr_id}', 'App\Http\Controllers\_Payment\Api\Generate\NewStudentInvoiceController@invoiceDetail');
        Route::get('new-student-invoice/show-invoice-component/{prr_id}', 'App\Http\Controllers\_Payment\Api\Generate\NewStudentInvoiceController@invoiceComponentDetail');
        Route::post('new-student-invoice/generate-one', 'App\Http\Controllers\_Payment\Api\Generate\NewStudentInvoiceController@generateOne');
        Route::post('new-student-invoice/delete-one', 'App\Http\Controllers\_Payment\Api\Generate\NewStudentInvoiceController@deleteOne');

        Route::get('student-invoice/index', 'App\Http\Controllers\_Payment\Api\Generate\StudentInvoiceController@index');
        Route::get('student-invoice/detail', 'App\Http\Controllers\_Payment\Api\Generate\StudentInvoiceController@detail');
        Route::get('student-invoice/header', 'App\Http\Controllers\_Payment\Api\Generate\StudentInvoiceController@header');
        Route::get('student-invoice/headerall', 'App\Http\Controllers\_Payment\Api\Generate\StudentInvoiceController@headerAll');
        Route::get('student-invoice/choice/{faculty}/{studyProgram}', 'App\Http\Controllers\_Payment\Api\Generate\StudentInvoiceController@choice');
        Route::get('student-invoice/choiceall', 'App\Http\Controllers\_Payment\Api\Generate\StudentInvoiceController@choiceAll');
        Route::post('student-invoice/student', 'App\Http\Controllers\_Payment\Api\Generate\StudentInvoiceController@studentGenerate');
        Route::post('student-invoice/bulk', 'App\Http\Controllers\_Payment\Api\Generate\StudentInvoiceController@studentBulkGenerate');
    });
});

Route::get('download', function(Request $request) {
    $storage = $request->query('storage');
    $type = $request->query('type');
    $filename = $request->query('filename');

    if ($storage && $type && $filename) {
        if ($storage == 'local') {
            if ($type == 'excel-log') {
                $path_arr = ['app', 'public', 'excel-logs', $filename];
                $path = storage_path(join(DIRECTORY_SEPARATOR, $path_arr));
                return response()->download($path, $filename);
            }
            if ($type == 'excel-template') {
                $path_arr = ['app', 'public', 'excel-templates', $filename];
                $path = storage_path(join(DIRECTORY_SEPARATOR, $path_arr));
                return response()->download($path, $filename);
            }
        }
    }
});
