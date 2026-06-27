<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CriteriaController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\BwmController;
use App\Http\Controllers\TopsisController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $totalCriteria = \App\Models\Criteria::count();
        $totalCustomer = \App\Models\Customer::count();
        return view('dashboard', compact('totalCriteria', 'totalCustomer'));
    })->name('dashboard');

    Route::resource('criterias', CriteriaController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('assessments', AssessmentController::class);

    Route::get('/bwm', [BwmController::class, 'index'])->name('bwm.index');
    Route::post('/bwm/calculate', [BwmController::class, 'calculate'])->name('bwm.calculate');

    Route::get('/topsis', [TopsisController::class, 'index'])->name('topsis.index');
    Route::post('/topsis/calculate', [TopsisController::class, 'calculate'])->name('topsis.calculate');
});
