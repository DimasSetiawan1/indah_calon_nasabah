<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CriteriaController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\BwmController;
use App\Http\Controllers\TopsisController;
use App\Http\Controllers\ApprovalController;
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
    // Akses MCE (Kelola Calon Nasabah)
    // ==========================================
    Route::middleware(['role:mce'])->group(function () {
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    });

    // ==========================================
    // Khusus Admin (BWM & Kriteria Internal)
    // ==========================================
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('criterias', CriteriaController::class);
        Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
        Route::get('/assessments/{assessment}', [AssessmentController::class, 'show'])->name('assessments.show');
        Route::post('/assessments', [AssessmentController::class, 'store'])->name('assessments.store');
        
        // Input & Hitung BWM
        Route::get('/bwm', [BwmController::class, 'index'])->name('bwm.index');
        Route::post('/bwm/calculate', [BwmController::class, 'calculate'])->name('bwm.calculate');
    });

    // ==========================================
    // Akses Lihat TOPSIS & Persetujuan (MCE, CAP, Admin)
    // ==========================================
    Route::middleware(['role:admin,cap,mce'])->group(function () {
        Route::get('/topsis', [TopsisController::class, 'index'])->name('topsis.index');
        Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::get('/customers/{customer}/detail', [CustomerController::class, 'showDetail'])->name('customers.detail');
    });

    // ==========================================
    // Eksekusi Persetujuan (Khusus CAP)
    // ==========================================
    Route::middleware(['role:cap'])->group(function () {
        Route::post('/approvals/{id}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/{id}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
        Route::post('/assessments/{customer}/status', [AssessmentController::class, 'updateStatus'])->name('assessments.updateStatus');
    });
});
