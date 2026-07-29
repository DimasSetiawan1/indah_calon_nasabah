@extends('layouts.app')

@section('title', 'Hasil Perangkingan Kelayakan')

@section('content')

@if(!$isReady)
    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center">
        <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-warning"></i>
        <div>
            <h6 class="fw-bold mb-1">Perhitungan Belum Dapat Dilakukan</h6>
            <p class="mb-0 small">{{ $errorMsg }}</p>
        </div>
    </div>
@else

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-trophy-fill text-warning me-2"></i> Peringkat Kelayakan Nasabah</h5>
                <p class="text-muted small mb-3">Diurutkan berdasarkan tingkat rekomendasi kelayakan terbesar.</p>
            </div>
            
            <div class="card-body p-0 border-top">
                <div class="p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" width="10%">Peringkat</th>
                                    <th width="15%">NIK</th>
                                    <th>Nama Nasabah</th>
                                    <th class="text-center" width="18%">Skor Kelayakan</th>
                                    <th class="text-center" width="15%">Status Kelayakan</th>
                                    <th class="text-center" width="18%">Status Persetujuan CAP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rankedCustomers as $customer)
                                <tr class="{{ $customer->calculated_rank <= 3 ? 'bg-success bg-opacity-10' : '' }}">
                                    <td class="text-center">
                                        @if($customer->calculated_rank == 1)
                                            <span class="badge bg-warning text-dark rounded-circle p-2 fs-6 shadow-sm"><i class="bi bi-trophy-fill"></i></span>
                                        @elseif($customer->calculated_rank == 2)
                                            <span class="badge bg-secondary rounded-circle p-2 fs-6 shadow-sm"><i class="bi bi-award-fill"></i></span>
                                        @elseif($customer->calculated_rank == 3)
                                            <span class="badge" style="background-color: #cd7f32;"><i class="bi bi-award-fill"></i></span>
                                        @else
                                            <span class="fw-bold text-secondary fs-5">{{ $customer->calculated_rank }}</span>
                                        @endif
                                    </td>
                                    <td class="text-secondary fw-medium">{{ $customer->nik }}</td>
                                    <td class="fw-bold text-dark">{{ $customer->name }}</td>
                                    <td class="text-center">
                                        <div class="fw-bold text-primary fs-5">{{ round($customer->calculated_score, 4) }}</div>
                                    </td>
                                    <td class="text-center">
                                        @if($customer->calculated_rank <= 3)
                                            <span class="badge bg-success w-100 py-2">Sangat Layak</span>
                                        @elseif($customer->calculated_score >= 0.5)
                                            <span class="badge bg-primary w-100 py-2">Layak</span>
                                        @else
                                            <span class="badge bg-danger w-100 py-2">Tidak Layak</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($customer->status_persetujuan === 'disetujui')
                                            <span class="badge bg-success w-100 py-2"><i class="bi bi-check-circle-fill me-1"></i> Disetujui</span>
                                        @elseif($customer->status_persetujuan === 'ditolak')
                                            <span class="badge bg-danger w-100 py-2"><i class="bi bi-x-circle-fill me-1"></i> Ditolak</span>
                                        @else
                                            <span class="badge bg-warning text-dark w-100 py-2"><i class="bi bi-clock-history me-1"></i> Tertunda</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endif

@endsection
