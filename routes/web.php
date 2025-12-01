<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmissionController;

Route::get('/', function() {
    return view('home');
});

Route::get('/guide', function(){
    return view('guide');
});

Route::get('/calculate', [EmissionController::class, 'show'])->name('show');

Route::post('/calculate', [EmissionController::class, 'calculate'])->name('calculate');