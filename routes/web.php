<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Auth::routes();

Route::get('/', fn () => redirect('/login'));

Route::group(['middleware' => ['auth', 'access']], function () {

    Route::get('/studyprogram', fn () => view('pages.studyprogram.index'));
    Route::get('/curriculum', fn () => view('pages.curriculum.index'));

    Route::get('/subjects', fn () => '-');
    Route::get('/learning-methods', fn () => '-');

    // Route::group([
    //     'prefix' => 'subjects',
    //     'middleware' => ['can:access_']
    // ], function(){
    //     Route::
    // });

});

// Static Routes
include __DIR__ . DIRECTORY_SEPARATOR . '_static-web.php';

// Payment
Route::group(['prefix' => 'payment', 'middleware' => ['auth', 'admin_access']], function () {

    // Settings
    Route::group(['prefix' => 'settings'], function () {
        // Component Invoices
        Route::get('component', 'App\Http\Controllers\_Payment\SettingsController@component')->name('payment.settings.component');
        Route::get('payment-rates', 'App\Http\Controllers\_Payment\SettingsController@paymentrates')->name('payment.settings.payment-rates');
        Route::get('payment-rates/detail/{id}', 'App\Http\Controllers\_Payment\SettingsController@paymentratesdetail')->name('payment.settings.payment-rates.detail');
        Route::get('subject-rates', 'App\Http\Controllers\_Payment\SettingsController@subjectrates')->name('payment.settings.subject-rates');
        Route::get('sks-rates', 'App\Http\Controllers\_Payment\SettingsController@sksrates')->name('payment.settings.sks-rates');
        Route::get('courserates/template', 'App\Http\Controllers\_Payment\Api\Settings\CourseRatesController@template');
        Route::get('credit-schema', 'App\Http\Controllers\_Payment\SettingsController@creditSchema')->name('payment.settings.credit-schema');
        Route::get('registration-form', 'App\Http\Controllers\_Payment\SettingsController@registrationForm')->name('payment.settings.registration-form');
        Route::get('academic-rules', 'App\Http\Controllers\_Payment\AcademicRules@index')->name('payment.settings.academic-rules');
    });

    // Generate
    Route::group(['prefix' => 'generate'], function () {
        Route::get('new-student-invoice', 'App\Http\Controllers\_Payment\GenerateController@newStudentInvoice')->name('payment.generate.new-student-invoice');
        Route::get('new-student-invoice/detail', 'App\Http\Controllers\_Payment\GenerateController@newStudentInvoiceDetail')->name('payment.generate.new-student-invoice-detail');

        Route::get('student-invoice', 'App\Http\Controllers\_Payment\GenerateController@StudentInvoice')->name('payment.generate.student-invoice');
        Route::get('student-invoice/detail', 'App\Http\Controllers\_Payment\GenerateController@StudentInvoiceDetail')->name('payment.generate.student-invoice-detail');

        Route::get('discount', 'App\Http\Controllers\_Payment\GenerateController@discount')->name('payment.generate.discount');
        Route::get('scholarship', 'App\Http\Controllers\_Payment\GenerateController@scholarship')->name('payment.generate.scholarship');

    });

    // Discount
    Route::group(['prefix' => 'discount'], function () {
        Route::get('index', 'App\Http\Controllers\_Payment\DiscountController@index')->name('payment.discount.index');
        Route::get('receiver', 'App\Http\Controllers\_Payment\DiscountController@receiver')->name('payment.discount.receiver');

    });

    // Scholarship
    Route::group(['prefix' => 'scholarship'], function () {
        Route::get('index', 'App\Http\Controllers\_Payment\ScholarshipController@index')->name('payment.scholarship.index');
        Route::get('receiver', 'App\Http\Controllers\_Payment\ScholarshipController@receiver')->name('payment.scholarship.receiver');
        Route::get('exportData', 'App\Http\Controllers\_Payment\Api\Scholarship\ScholarshipReceiverController@exportData');
    });

    // Approval
    Route::group(['prefix' => 'approval'], function () {
        Route::get('manual-payment', 'App\Http\Controllers\_Payment\ApprovalController@manualPayment')->name('payment.approval.manual-payment.index');

        Route::group(['prefix' => 'dispensation'], function () {
            Route::get('index', 'App\Http\Controllers\_Payment\ApprovalController@dispensation')->name('payment.approval.dispensation.index');
        });

        Route::group(['prefix' => 'credit'], function () {
            Route::get('index', 'App\Http\Controllers\_Payment\ApprovalController@credit')->name('payment.approval.credit.index');
        });
    });

    // Report
    Route::group(['prefix' => 'report'], function () {
        Route::group(['prefix' => 'old-student-invoice'], function () {
            Route::get('/studyprogram', 'App\Http\Controllers\_Payment\ReportController@oldStudentPerStudyprogram');
            Route::get('/student', 'App\Http\Controllers\_Payment\ReportController@oldStudentPerStudent');
            Route::get('/download-perstudent', 'App\Http\Controllers\_Payment\Api\ReportControllerApi@studentExport');
            Route::get('/program-study/{programStudy}', 'App\Http\Controllers\_Payment\ReportController@oldStudentDetail');
        });
        Route::group(['prefix' => 'new-student-invoice'], function(){
            Route::get('/', 'App\Http\Controllers\_Payment\ReportController@newStudent');
            Route::get('/download-perstudent', 'App\Http\Controllers\_Payment\Api\ReportControllerApi@studentExport');
            Route::get('/program-study/{programStudy}', 'App\Http\Controllers\_Payment\ReportController@newStudentDetail');
        });
        Route::group(['prefix' => 'old-student-receivables'], function(){
            Route::get('/', 'App\Http\Controllers\_Payment\ReportController@oldStudentReceivable');
            Route::get('/program-study/{programStudy}', 'App\Http\Controllers\_Payment\ReportController@oldStudentReceivableDetail');
        });
        Route::group(['prefix' => 'new-student-receivables'], function(){
            Route::get('/', 'App\Http\Controllers\_Payment\ReportController@newStudentReceivables');
            Route::get('/program-study/{programStudy}', 'App\Http\Controllers\_Payment\ReportController@newStudentReceivableDetail');
        });
        Route::group(['prefix' => 'registrant-invoice'], function(){
            Route::get('/', 'App\Http\Controllers\_Payment\ReportController@registrantInvoice');
        });
    });

    // Student Routes
    Route::group([
        'middleware' => ['student_access'],
        'excluded_middleware' => ['admin_access'],
    ], function () {
        Route::group(['prefix' => 'student-invoice'], function () {
            Route::get('/', 'App\Http\Controllers\_Payment\StudentInvoiceController@index')->name('payment.student-invoice.index');
            Route::get('{prr_id}/proceed-payment', 'App\Http\Controllers\_Payment\StudentInvoiceController@proceedPayment');
            Route::get('invoice-cicilan', 'App\Http\Controllers\_Payment\StudentInvoiceController@invoiceCicilan');
        });

        Route::get('/student-balance', 'App\Http\Controllers\_Payment\StudentBalanceController@index')->name('payment.student-balance.index');

        Route::group(['prefix' => 'student-dispensation'], function () {
            Route::get('index', 'App\Http\Controllers\_Payment\StudentDispensationController@index')->name('payment.student-dispensation.index');
        });
        Route::group(['prefix' => 'student-credit'], function () {
            Route::get('index', 'App\Http\Controllers\_Payment\StudentCreditController@index')->name('payment.student-credit.index');
        });
    });

    // Development Testing Routes
    Route::group(['prefix' => 'dev-test'], function() {
        Route::get('/', 'App\Http\Controllers\_Payment\DevTestController@index');
    });

    // Settings Route
    Route::group(['prefix' => 'settings'], function() {
        Route::get('/', 'App\Http\Controllers\_Payment\SettingsController@index')->name('payment.settings.index');
        Route::post('/update', 'App\Http\Controllers\_Payment\SettingsController@update')->name('payment.settings.update');
    });

});

Route::get('/file/{from}/{id}', 'App\Http\Controllers\_Payment\FileController@getFile')->name('file');

