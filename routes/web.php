<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmissionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\UserController;

Route::get('/', function() {
    return view('home');
});

Route::get('/guide', function(){
    return view('guide');
});

Route::get('/register', function(){
    return view('register');
});

Route::get('/login', function(){
    return view('login');
});

Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/calculate', [EmissionController::class, 'show'])->name('show');

Route::post('/calculate', [EmissionController::class, 'calculate'])->name('calculate');

// Protected routes
Route::middleware(['auth'])->group(function (){

    Route::get('/history', [HistoryController::class, 'view'])->name('history.view');
    Route::delete('/history/delete', [HistoryController::class, 'delete'])->name('history.delete');

    Route::get('/profile', function(){
        return view('profile');
    });

    Route::put('/profile/update', [UserController::class, 'update'])->name('profile.update');
    Route::post('/profile/delete', [UserController::class, 'delete'])->name('profile.delete');    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});



