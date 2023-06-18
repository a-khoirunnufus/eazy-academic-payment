<?php

use Illuminate\Support\Facades\Route;

Route::get('dt/invoice', 'App\Http\Controllers\_Student\Api\TempResourceController@invoice');
Route::get('dt/payment', 'App\Http\Controllers\_Student\Api\TempResourceController@payment');