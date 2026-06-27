@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card bg-primary text-white text-center p-3">
            <h3>{{ $totalCriteria }}</h3>
            <p>Total Kriteria</p>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card bg-success text-white text-center p-3">
            <h3>{{ $totalCustomer }}</h3>
            <p>Total Calon Nasabah</p>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">Informasi Sistem</div>
            <div class="card-body">
                <p>Selamat datang di <strong>Sistem Rekomendasi Kelayakan Calon Nasabah</strong> menggunakan integrasi metode <strong>Best Worst Method (BWM)</strong> dan <strong>TOPSIS</strong>.</p>
                <ol>
                    <li>Kelola <strong>Data Kriteria</strong> untuk menentukan kriteria penilaian.</li>
                    <li>Kelola <strong>Data Nasabah</strong> untuk menambah calon nasabah.</li>
                    <li>Beri <strong>Penilaian</strong> pada masing-masing nasabah terhadap kriteria.</li>
                    <li>Hitung bobot kriteria pada menu <strong>Perhitungan BWM</strong>.</li>
                    <li>Lihat hasil akhir dan perankingan pada menu <strong>Hasil TOPSIS</strong>.</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection
