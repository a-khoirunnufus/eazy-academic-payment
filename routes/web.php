<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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