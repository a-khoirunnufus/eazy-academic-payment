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
        Route::get('paymentrates/getdetailschemabyid/{cs_id}', 'App\Http\Controllers\_Payment\Api\Settings\PaymentRatesController@getDetailSchemaById');
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
        Route::post('new-student-invoice/generate-by-scope', 'App\Http\Controllers\_Payment\Api\Generate\NewStudentInvoiceController@generateByScope');
        Route::post('new-student-invoice/generate-by-scopes', 'App\Http\Controllers\_Payment\Api\Generate\NewStudentInvoiceController@generateByScopes');
        Route::post('new-student-invoice/generate-all', 'App\Http\Controllers\_Payment\Api\Generate\NewStudentInvoiceController@generateAll');
        Route::post('new-student-invoice/delete-one', 'App\Http\Controllers\_Payment\Api\Generate\NewStudentInvoiceController@deleteOne');
        Route::post('new-student-invoice/delete-by-scope', 'App\Http\Controllers\_Payment\Api\Generate\NewStudentInvoiceController@deleteByScope');
        Route::post('new-student-invoice/delete-all', 'App\Http\Controllers\_Payment\Api\Generate\NewStudentInvoiceController@deleteAll');
        Route::get('new-student-invoice/get-tree-generate-all', 'App\Http\Controllers\_Payment\Api\Generate\NewStudentInvoiceController@getTreeGenerateAll');
        Route::get('new-student-invoice/get-tree-generate-faculty', 'App\Http\Controllers\_Payment\Api\Generate\NewStudentInvoiceController@getTreeGenerateFaculty');
        Route::get('new-student-invoice/get-tree-generate-studyprogram', 'App\Http\Controllers\_Payment\Api\Generate\NewStudentInvoiceController@getTreeGenerateStudyprogram');

        Route::get('student-invoice/index', 'App\Http\Controllers\_Payment\Api\Generate\StudentInvoiceController@index');
        Route::get('student-invoice/detail', 'App\Http\Controllers\_Payment\Api\Generate\StudentInvoiceController@detail');
        Route::get('student-invoice/header', 'App\Http\Controllers\_Payment\Api\Generate\StudentInvoiceController@header');
        Route::get('student-invoice/headerall', 'App\Http\Controllers\_Payment\Api\Generate\StudentInvoiceController@headerAll');
        Route::get('student-invoice/choice/{faculty}/{studyProgram}', 'App\Http\Controllers\_Payment\Api\Generate\StudentInvoiceController@choice');
        Route::get('student-invoice/choiceall', 'App\Http\Controllers\_Payment\Api\Generate\StudentInvoiceController@choiceAll');
        Route::post('student-invoice/student', 'App\Http\Controllers\_Payment\Api\Generate\StudentInvoiceController@studentGenerate');
        Route::post('student-invoice/bulk', 'App\Http\Controllers\_Payment\Api\Generate\StudentInvoiceController@studentBulkGenerate');
        Route::delete('student-invoice/delete/{id}', 'App\Http\Controllers\_Payment\Api\Generate\StudentInvoiceController@delete');
        Route::delete('student-invoice/deleteBulk/{faculty}/{studyProgram}', 'App\Http\Controllers\_Payment\Api\Generate\StudentInvoiceController@deleteBulk');
        Route::get('student-invoice/log-invoice', 'App\Http\Controllers\_Payment\Api\Generate\StudentInvoiceController@logGenerate');

        Route::group(['prefix' => 'discount'], function(){
            Route::get('index', 'App\Http\Controllers\_Payment\Api\Generate\DiscountGenerateController@index');
            Route::post('generate', 'App\Http\Controllers\_Payment\Api\Generate\DiscountGenerateController@generate');
            Route::post('generateBulk', 'App\Http\Controllers\_Payment\Api\Generate\DiscountGenerateController@generateBulk');
            Route::delete('delete/{id}', 'App\Http\Controllers\_Payment\Api\Generate\DiscountGenerateController@delete');
            Route::post('deleteBulk', 'App\Http\Controllers\_Payment\Api\Generate\DiscountGenerateController@deleteBulk');
        });

        Route::group(['prefix' => 'scholarship'], function(){
            Route::get('index', 'App\Http\Controllers\_Payment\Api\Generate\ScholarshipGenerateController@index');
            Route::post('generate', 'App\Http\Controllers\_Payment\Api\Generate\ScholarshipGenerateController@generate');
            Route::post('generateBulk', 'App\Http\Controllers\_Payment\Api\Generate\ScholarshipGenerateController@generateBulk');
            Route::delete('delete/{id}', 'App\Http\Controllers\_Payment\Api\Generate\ScholarshipGenerateController@delete');
            Route::post('deleteBulk', 'App\Http\Controllers\_Payment\Api\Generate\ScholarshipGenerateController@deleteBulk');
        });
    });

    Route::group(['prefix' => 'discount'], function(){
        Route::get('index', 'App\Http\Controllers\_Payment\Api\Discount\DiscountController@index');
        Route::get('period', 'App\Http\Controllers\_Payment\Api\Discount\DiscountController@period');
        Route::post('store', 'App\Http\Controllers\_Payment\Api\Discount\DiscountController@store');
        Route::delete('delete/{id}', 'App\Http\Controllers\_Payment\Api\Discount\DiscountController@delete');
        Route::post('exportData', 'App\Http\Controllers\_Payment\Api\Discount\DiscountController@exportData');
    });

    Route::group(['prefix' => 'discount-receiver'], function(){
        Route::get('index', 'App\Http\Controllers\_Payment\Api\Discount\DiscountReceiverController@index');
        Route::get('discount', 'App\Http\Controllers\_Payment\Api\Discount\DiscountReceiverController@discount');
        Route::get('student', 'App\Http\Controllers\_Payment\Api\Discount\DiscountReceiverController@student');
        Route::get('period/{md_id}', 'App\Http\Controllers\_Payment\Api\Discount\DiscountReceiverController@period');
        Route::post('store', 'App\Http\Controllers\_Payment\Api\Discount\DiscountReceiverController@store');
        Route::delete('delete/{id}', 'App\Http\Controllers\_Payment\Api\Discount\DiscountReceiverController@delete');
        Route::get('faculty/{id}', 'App\Http\Controllers\_Payment\Api\Discount\DiscountReceiverController@studyProgram');
        Route::post('exportData', 'App\Http\Controllers\_Payment\Api\Discount\DiscountReceiverController@exportData');
    });

    Route::group(['prefix' => 'scholarship'], function(){
        Route::get('index', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipController@index');
        Route::get('period', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipController@period');
        Route::post('store', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipController@store');
        Route::delete('delete/{id}', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipController@delete');
        Route::post('exportData', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipController@exportData');
    });

    Route::group(['prefix' => 'scholarship-receiver'], function(){
        Route::get('index', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipReceiverController@index');
        Route::get('scholarship', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipReceiverController@scholarship');
        Route::get('student', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipReceiverController@student');
        Route::get('study-program', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipReceiverController@study_program');
        Route::get('period/{md_id}', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipReceiverController@period');
        Route::post('store', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipReceiverController@store');
        Route::delete('delete/{id}', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipReceiverController@delete');
        Route::post('exportData', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipReceiverController@exportData');
    });

    Route::group(['prefix' => 'approval-credit'], function(){
        Route::get('index', 'App\Http\Controllers\_Payment\Api\Approval\CreditSubmissionController@index');
        Route::post('store', 'App\Http\Controllers\_Payment\Api\Approval\CreditSubmissionController@store');
        Route::post('decline', 'App\Http\Controllers\_Payment\Api\Approval\CreditSubmissionController@decline');
        // Route::get('scholarship', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipReceiverController@scholarship');
        // Route::get('student', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipReceiverController@student');
        // Route::get('study-program', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipReceiverController@study_program');
        // Route::get('period/{md_id}', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipReceiverController@period');
        // Route::delete('delete/{id}', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipReceiverController@delete');
        // Route::post('exportData', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipReceiverController@exportData');
    });


    Route::group(['prefix' => 'approval'], function(){
        Route::get('/', 'App\Http\Controllers\_Payment\Api\Approval\ManualPaymentController@index');
        Route::post('{prrb_id}/process-approval', 'App\Http\Controllers\_Payment\Api\Approval\ManualPaymentController@processApproval');
    });
});

// REPORT GROUP ROUTE
Route::group(['prefix' => 'report'], function(){
    Route::group(['prefix' => 'old-student-invoice'], function(){
        Route::get('/', 'App\Http\Controllers\_Payment\Api\ReportControllerApi@oldStudent');
        Route::get('/student-history/{student_number}', 'App\Http\Controllers\_Payment\Api\ReportControllerApi@oldStudentHistory');
        Route::post('/export', 'App\Http\Controllers\_Payment\Api\ReportControllerApi@exportOldStudentPerProdi');
    });
    Route::group(['prefix' => 'new-student-invoice'], function(){
        Route::get('/', 'App\Http\Controllers\_Payment\Api\ReportControllerApi@newStudent');
        Route::get('/student-history/{student_number}', 'App\Http\Controllers\_Payment\Api\ReportControllerApi@newStudentHistory');
    });
    Route::get('/getProdi/{faculty}', 'App\Http\Controllers\_Payment\Api\ReportControllerApi@getProdi');
});

// STUDENT GROUP ROUTE
Route::group(['prefix' => 'student'], function(){
    Route::get('detail', 'App\Http\Controllers\_Student\Api\StudentController@detail');

    Route::group(['prefix' => 'payment'], function() {
        Route::get('/', 'App\Http\Controllers\_Student\Api\PaymentController@index');
        Route::get('{prr_id}', 'App\Http\Controllers\_Student\Api\PaymentController@detail');

        Route::get('{prr_id}/ppm', 'App\Http\Controllers\_Student\Api\PaymentController@getPpm');
        Route::get('{prr_id}/credit-schemas', 'App\Http\Controllers\_Student\Api\PaymentController@creditSchemas');
        Route::get('{prr_id}/payment-option-preview', 'App\Http\Controllers\_Student\Api\PaymentController@paymentOptionPreview');
        Route::post('{prr_id}/select-option', 'App\Http\Controllers\_Student\Api\PaymentController@selectPaymentOption');
        Route::post('{prr_id}/reset-payment', 'App\Http\Controllers\_Student\Api\PaymentController@resetPayment');

        Route::get('{prr_id}/bill', 'App\Http\Controllers\_Student\Api\PaymentController@getBills');
        Route::get('{prr_id}/bill/{prrb_id}', 'App\Http\Controllers\_Student\Api\PaymentController@billDetail');
        Route::get('{prr_id}/bill/{prrb_id}/evidence', 'App\Http\Controllers\_Student\Api\PaymentController@getEvidence');
        Route::post('{prr_id}/bill/{prrb_id}/evidence', 'App\Http\Controllers\_Student\Api\PaymentController@uploadEvidence');
        Route::post('{prr_id}/bill/{prrb_id}/select-method', 'App\Http\Controllers\_Student\Api\PaymentController@selectPaymentMethod');
        Route::post('{prr_id}/bill/{prrb_id}/reset-method', 'App\Http\Controllers\_Student\Api\PaymentController@resetPaymentMethod');
    });

    Route::group(['prefix' => 'payment-method'], function() {
        Route::get('/', 'App\Http\Controllers\_Student\Api\PaymentMethodController@index');
        Route::get('{method_code}', 'App\Http\Controllers\_Student\Api\PaymentMethodController@detail');
    });

    Route::group(['prefix' => 'credit'], function(){
        Route::get('index', 'App\Http\Controllers\_Student\Api\CreditController@index');
        Route::post('store', 'App\Http\Controllers\_Student\Api\CreditController@store');
        Route::delete('delete/{id}', 'App\Http\Controllers\_Student\Api\CreditController@delete');
        // Route::get('scholarship', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipReceiverController@scholarship');
        // Route::get('student', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipReceiverController@student');
        // Route::get('study-program', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipReceiverController@study_program');
        // Route::get('period/{md_id}', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipReceiverController@period');
        // Route::post('exportData', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipReceiverController@exportData');
    });
});


// PAYMENT SIMULATOR
Route::post('payment-simulator/pay', 'App\Http\Controllers\_PaymentSimulator\PaymentSimulatorController@pay');

// DOWNLOAD LOCAL
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

// DOWNLOAD CLOUD
Route::get('download-cloud', function(Request $request) {
    if(config('app.disable_cloud_storage')) {
        return response()->json([
            'error' => 'Resource not found',
        ], 404);
    }

    $path = $request->query('path');
    if (!$path) {
        return response()->json(['error' => 'path params must be defined!'], 400);
    }
    return \Illuminate\Support\Facades\Storage::disk('minio_read')->download($path);
});
