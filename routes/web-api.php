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
        Route::post('component/import', 'App\Http\Controllers\_Payment\Api\Settings\ComponentInvoiceController@import');

        Route::get('courserates/index', 'App\Http\Controllers\_Payment\Api\Settings\CourseRatesController@index');
        Route::get('courserates/studyprogram', 'App\Http\Controllers\_Payment\Api\Settings\CourseRatesController@getStudyProgram');
        Route::get('courserates/course/{id}', 'App\Http\Controllers\_Payment\Api\Settings\CourseRatesController@getMataKuliah');
        Route::get('courserates/getbycourseid/{id}', 'App\Http\Controllers\_Payment\Api\Settings\CourseRatesController@getCourseRateByCourseId');
        Route::post('courserates/store', 'App\Http\Controllers\_Payment\Api\Settings\CourseRatesController@store');
        Route::delete('courserates/delete/{id}', 'App\Http\Controllers\_Payment\Api\Settings\CourseRatesController@delete');

        Route::get('paymentrates/index', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@index');
        Route::get('paymentrates/detail/{id}', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@detail');
        Route::get('paymentrates/period', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@getPeriod');
        Route::get('paymentrates/path', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@getPath');
        Route::get('paymentrates/component', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@getComponent');
        Route::get('paymentrates/schema', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@getSchema');
        Route::post('paymentrates/store', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@store');
        Route::post('paymentrates/update', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@update');
        Route::delete('paymentrates/delete/{id}', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@delete');
        Route::delete('paymentrates/deletecomponent/{id}', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@deletecomponent');

        Route::get('credit-schema/index', 'App\Http\Controllers\_Payment\Api\Settings\CreditSchemaController@index');
        Route::get('credit-schema/show/{id}', 'App\Http\Controllers\_Payment\Api\Settings\CreditSchemaController@show');
        Route::post('credit-schema/store', 'App\Http\Controllers\_Payment\Api\Settings\CreditSchemaController@store');
        Route::put('credit-schema/update/{id}', 'App\Http\Controllers\_Payment\Api\Settings\CreditSchemaController@update');
        Route::delete('credit-schema/delete/{id}', 'App\Http\Controllers\_Payment\Api\Settings\CreditSchemaController@delete');
    });

    Route::group(['prefix' => 'generate'], function(){
        Route::get('new-student-invoice/index', 'App\Http\Controllers\_Payment\Api\Generate\NewStudentInvoiceController@index');
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
