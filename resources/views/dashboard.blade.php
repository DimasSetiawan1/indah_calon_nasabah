@extends('layouts.app')

@section('title', 'Dashboard Utama')

@section('content')
<div class="row mb-4">
    <div class="col-xl-6 col-lg-6 mb-4 mb-lg-0">
        <div class="card h-100 border-0" style="background: linear-gradient(135deg, #0b5ed7 0%, #0a4ebd 100%);">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="bg-white bg-opacity-25 rounded-circle p-3 d-inline-flex me-4 shadow-sm">
                    <i class="bi bi-list-check text-white fs-1"></i>
                </div>
                <div>
                    <h2 class="text-white fw-bold mb-1 display-5">{{ $totalCriteria }}</h2>
                    <p class="text-white text-opacity-75 mb-0 fw-medium fs-5">Total Kriteria Penilaian</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-lg-6">
        <div class="card h-100 border-0" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="bg-white bg-opacity-25 rounded-circle p-3 d-inline-flex me-4 shadow-sm">
                    <i class="bi bi-people-fill text-white fs-1"></i>
                </div>
                <div>
                    <h2 class="text-white fw-bold mb-1 display-5">{{ $totalCustomer }}</h2>
                    <p class="text-white text-opacity-75 mb-0 fw-medium fs-5">Total Calon Nasabah</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0 mt-2">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-5 d-flex align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-info-circle-fill text-primary me-2"></i> Petunjuk Penggunaan Sistem</h5>
            </div>
            <div class="card-body p-5">
                <p class="fs-5 text-secondary mb-5">Selamat datang di <strong>Sistem Rekomendasi Kelayakan Calon Nasabah</strong> menggunakan integrasi metode <strong>Best Worst Method (BWM)</strong> dan <strong>TOPSIS</strong>.</p>
                
                <div class="row g-5">
                    <div class="col-md-4">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-weight: bold; font-size: 1.2rem;">1</div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fw-bold mb-1">Master Data</h6>
                                <p class="text-muted mb-0 small">Kelola Data Kriteria untuk parameter penilaian dan tambahkan Data Nasabah yang akan dinilai kelayakannya.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-weight: bold; font-size: 1.2rem;">2</div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fw-bold mb-1">Input Penilaian</h6>
                                <p class="text-muted mb-0 small">Beri penilaian pada masing-masing nasabah terhadap kriteria yang telah ditetapkan melalui menu Penilaian.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-weight: bold; font-size: 1.2rem;">3</div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fw-bold mb-1">Analisis & Hasil</h6>
                                <p class="text-muted mb-0 small">Hitung bobot kriteria di menu Analisis BWM, lalu lihat hasil perangkingan kelayakan di menu Hasil TOPSIS.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
