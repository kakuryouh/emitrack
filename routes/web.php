<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalculateController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;

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

Route::get('/calculate', [CalculateController::class, 'viewCalculator'])->name('show');

Route::post('/calculate', [CalculateController::class, 'calculate'])->name('calculate');

Route::get('/compare-cost', [CalculateController::class, 'viewCompare']);

Route::post('/compare-cost', [CalculateController::class, 'compare']);

Route::get('/dashboard', [DashboardController::class, 'viewDashboard']);

Route::post('/log-travel', [CalculateController::class, 'addTravel']);


// Protected routes
Route::middleware(['auth'])->group(function (){

    Route::get('/history', [DashboardController::class, 'viewLogs'])->name('logs.view');
    Route::delete('/history/delete', [DashboardController::class, 'DeleteLog'])->name('log.delete');

    Route::get('/profile', function(){
        return view('profile');
    });

    Route::put('/profile/update', [UserController::class, 'update'])->name('profile.update');
    Route::post('/profile/delete', [UserController::class, 'delete'])->name('profile.delete');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});



