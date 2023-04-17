<?php

// Test
Route::get('school-year', 'App\Http\Controllers\Api\TempResourceController@schoolYear');
Route::get('class-year', 'App\Http\Controllers\Api\TempResourceController@classYear');
Route::get('faculty', 'App\Http\Controllers\Api\TempResourceController@faculty');
Route::get('study-program', 'App\Http\Controllers\Api\TempResourceController@studyProgram');
Route::get('registration-path', 'App\Http\Controllers\Api\TempResourceController@registrationPath');
Route::get('registration-period', 'App\Http\Controllers\Api\TempResourceController@registrationPeriod');

// Datatable Routes
// Menu Setting
Route::get('dt/invoice-component', 'App\Http\Controllers\Api\TempResourceController@invoiceComponent');
Route::get('dt/instalment-template', 'App\Http\Controllers\Api\TempResourceController@instalmentTemplate');
Route::get('dt/rates', 'App\Http\Controllers\Api\TempResourceController@rates');
Route::get('dt/rates-per-course', 'App\Http\Controllers\Api\TempResourceController@ratesPerCourse');
Route::get('dt/registration-form', 'App\Http\Controllers\Api\TempResourceController@registrationForm');


Route::get('dt/academic-rules', 'App\Http\Controllers\_Payment\Api\AcademicRulesApi@academicRules');
Route::post('dt/academic-rules/add', 'App\Http\Controllers\_Payment\Api\AcademicRulesApi@addData');
// Menu Generate
Route::get('dt/registrant-invoice', 'App\Http\Controllers\Api\TempResourceController@registrantInvoice');
Route::get('dt/old-student-invoice', 'App\Http\Controllers\Api\TempResourceController@oldStudentInvoice');
Route::get('dt/new-student-invoice', 'App\Http\Controllers\Api\TempResourceController@newStudentInvoice');
Route::get('dt/student-invoice-detail', 'App\Http\Controllers\Api\TempResourceController@studentInvoiceDetail');
Route::get('dt/other-invoice', 'App\Http\Controllers\Api\TempResourceController@otherInvoice');
Route::get('dt/other-invoice-detail', 'App\Http\Controllers\Api\TempResourceController@otherInvoiceDetail');
// Menu Report
Route::get('dt/report-old-student-invoice-per-study-program', 'App\Http\Controllers\Api\TempResourceController@reportOldStudentInvoicePerStudyProgram');
Route::get('dt/report-old-student-invoice-per-student', 'App\Http\Controllers\Api\TempResourceController@reportOldStudentInvoicePerStudent');
Route::get('dt/report-old-student-payment-history', 'App\Http\Controllers\Api\TempResourceController@reportOldStudentPaymentHistory');
Route::get('dt/report-new-student-invoice-per-study-program', 'App\Http\Controllers\Api\TempResourceController@reportNewStudentInvoicePerStudyProgram');
Route::get('dt/report-new-student-invoice-per-student', 'App\Http\Controllers\Api\TempResourceController@reportNewStudentInvoicePerStudent');
Route::get('dt/report-new-student-payment-history', 'App\Http\Controllers\Api\TempResourceController@reportNewStudentPaymentHistory');
Route::get('dt/report-registrant-invoice-per-student', 'App\Http\Controllers\Api\TempResourceController@reportRegistrantInvoice');
Route::get('dt/report-registrant-payment-history', 'App\Http\Controllers\Api\TempResourceController@reportRegistrantPaymentHistory');
Route::get('dt/report-old-student-receivables-per-study-program', 'App\Http\Controllers\Api\TempResourceController@reportOldStudentReceivablesPerStudyProgram');
Route::get('dt/report-old-student-receivables-per-student', 'App\Http\Controllers\Api\TempResourceController@reportOldStudentReceivablesPerStudent');
Route::get('dt/report-new-student-receivables-per-study-program', 'App\Http\Controllers\Api\TempResourceController@reportNewStudentReceivablesPerStudyProgram');
Route::get('dt/report-new-student-receivables-per-student', 'App\Http\Controllers\Api\TempResourceController@reportNewStudentReceivablesPerStudent');
