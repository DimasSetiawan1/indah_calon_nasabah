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

    // ==========================================
    // Akses MCE & Admin
    // ==========================================
    Route::middleware(['role:mce,admin'])->group(function () {
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    });

    // ==========================================
    // Khusus Admin
    // ==========================================
    Route::middleware(['role:admin'])->group(function () {
        // Kelola Kriteria
        Route::resource('criterias', CriteriaController::class);
        
        // Kelola Nasabah (Edit, Delete)
        Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

        // BWM & Penilaian
        Route::resource('assessments', AssessmentController::class);
        Route::get('/bwm', [BwmController::class, 'index'])->name('bwm.index');
        Route::post('/bwm/calculate', [BwmController::class, 'calculate'])->name('bwm.calculate');
    });

    // ==========================================
    // Khusus Admin & CAP
    // ==========================================
    Route::middleware(['role:admin,cap'])->group(function () {
        // TOPSIS (Admin melihat hasil, CAP menyetujui)
        Route::get('/topsis', [TopsisController::class, 'index'])->name('topsis.index');
    });
});
