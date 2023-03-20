<?php

Route::group(['middleware' => ['auth']], function(){

    Route::resource('curriculum', 'App\Http\Controllers\Api\CurriculumController');
    Route::resource('studyprogram', 'App\Http\Controllers\Api\StudyprogramController');

    // File Resource (if you want move this, make sure update public/plugins/filepond.js)
    Route::post('resources/upload', 'App\Http\Controllers\Api\ResourceController@upload');
    Route::delete('resources/{id}', 'App\Http\Controllers\Api\ResourceController@destroy');
});