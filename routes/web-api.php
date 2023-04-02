<?php

Route::group(['middleware' => ['auth']], function(){

    Route::resource('curriculum', 'App\Http\Controllers\Api\CurriculumController');
    Route::resource('studyprogram', 'App\Http\Controllers\Api\StudyprogramController');

    // File Resource (if you want move this, make sure update public/plugins/filepond.js)
    Route::post('resources/upload', 'App\Http\Controllers\Api\ResourceController@upload');
    Route::delete('resources/{id}', 'App\Http\Controllers\Api\ResourceController@destroy');
});

// Test
Route::get('dt/invoice-component', 'App\Http\Controllers\Api\TempResourceController@invoiceComponent');
Route::get('dt/instalment-template', 'App\Http\Controllers\Api\TempResourceController@instalmentTemplate');
Route::get('dt/rates', 'App\Http\Controllers\Api\TempResourceController@rates');
Route::get('dt/rates-per-course', 'App\Http\Controllers\Api\TempResourceController@ratesPerCourse');
Route::get('dt/registration-form', 'App\Http\Controllers\Api\TempResourceController@registrationForm');
Route::get('dt/academic-rules', 'App\Http\Controllers\Api\TempResourceController@academicRules');

Route::get('dt/registrant-invoice', 'App\Http\Controllers\Api\TempResourceController@registrantInvoice');
Route::get('dt/old-student-invoice', 'App\Http\Controllers\Api\TempResourceController@oldStudentInvoice');
Route::get('dt/new-student-invoice', 'App\Http\Controllers\Api\TempResourceController@newStudentInvoice');
Route::get('dt/student-invoice-detail', 'App\Http\Controllers\Api\TempResourceController@studentInvoiceDetail');
Route::get('dt/other-invoice', 'App\Http\Controllers\Api\TempResourceController@otherInvoice');
Route::get('dt/other-invoice-detail', 'App\Http\Controllers\Api\TempResourceController@otherInvoiceDetail');

Route::get('dt/student/student-invoice', 'App\Http\Controllers\Api\TempResourceController@studentStudentInvoice');