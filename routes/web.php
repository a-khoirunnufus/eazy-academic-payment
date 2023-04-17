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
        Route::get('subject-rates', 'App\Http\Controllers\_Payment\SettingsController@subjectrates')->name('payment.settings.subject-rates');
        Route::get('credit-schema', 'App\Http\Controllers\_Payment\SettingsController@creditSchema')->name('payment.settings.credit-schema');
    });
});
