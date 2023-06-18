<?php

use Illuminate\Support\Facades\Route;

Route::get('/payment', fn() => view('_student.payment'));
Route::get('/proceed-payment', fn() => view('_student.proceed-payment'));