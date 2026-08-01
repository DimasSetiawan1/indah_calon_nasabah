@extends('layouts.app')

@section('title', 'Detail Evaluasi & Kelayakan Nasabah')

@section('content')
    <div class="mb-3">
        <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('topsis.index') }}"
            class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1 text-primary"><i class="bi bi-person-lines-fill me-2"></i> Detail Kelayakan Nasabah
                </h5>
                <p class="text-muted small mb-0">Rincian profil data diri dan hasil evaluasi indikator kelayakan kredit.</p>
            </div>
            <div>
                @if($customer->status_persetujuan === 'disetujui')
                    <span class="badge bg-success px-3 py-2 rounded-pill fs-6"><i class="bi bi-check-circle-fill me-1"></i>
                        Disetujui CAP</span>
                @elseif($customer->status_persetujuan === 'ditolak')
                    <span class="badge bg-danger px-3 py-2 rounded-pill fs-6"><i class="bi bi-x-circle-fill me-1"></i> Ditolak
                        CAP</span>
                @else
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fs-6"><i
                            class="bi bi-clock-history me-1"></i> Status Persetujuan: Tertunda</span>
                @endif
            </div>
        </div>

        <div class="card-body p-4">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-card-text me-1 text-secondary"></i> Data
                Pribadi Nasabah</h6>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <small class="text-muted d-block">NIK (Nomor Induk Kependudukan)</small>
                    <span class="fw-bold text-dark fs-6">{{ $customer->nik }}</span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Nama Lengkap Nasabah</small>
                    <span class="fw-bold text-dark fs-6">{{ $customer->name }}</span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Nomor HP / WhatsApp</small>
                    <span class="fw-bold text-dark fs-6">{{ $customer->phone ?? '-' }}</span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Alamat Lengkap</small>
                    <span class="fw-bold text-dark fs-6">{{ $customer->address ?? '-' }}</span>
                </div>
            </div>

            {{-- <h6 class="fw-bold text-dark border-bottom pb-2 mt-4 mb-3"><i
                    class="bi bi-sliders me-1 text-secondary"></i>
                Rincian Indikator Penilaian</h6>

            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="35%">Indikator Penilaian</th>
                            <th>Pilihan Indikator Kelayakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($criterias as $index => $criteria)
                        @php
                        $userAssessment = isset($assessments[$criteria->id]) ? $assessments[$criteria->id]->value : null;
                        $selectedSub = $criteria->subcriterias->firstWhere('score', $userAssessment);
                        @endphp
                        <tr>
                            <td class="text-center font-bold">{{ $index + 1 }}</td>
                            <td class="fw-medium text-dark">{{ $criteria->name }}</td>
                            <td>
                                @if($selectedSub)
                                <span class="fw-semibold text-primary"><i class="bi bi-check2-square me-1"></i>
                                    {{ $selectedSub->name }}</span>
                                @else
                                <span class="text-muted small italic">- Belum dipilih -</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div> --}}

            <h6 class="fw-bold text-dark border-bottom pb-2 mt-4 mb-3"><i
                    class="bi bi-info-circle-fill me-1 text-secondary"></i> Penjelasan & Catatan Rekomendasi System</h6>

            @php
                $isEligible = ($customer->topsis_rank !== null && $customer->topsis_rank <= 3) || ($customer->topsis_score >= 0.5);
                $isRejected = ($customer->status_persetujuan === 'ditolak') || (!$isEligible && $customer->topsis_score !== null);
            @endphp

            @if($isRejected)
                <div class="card border-0 bg-danger bg-opacity-10 rounded-3 p-4">
                    <div class="d-flex align-items-start">
                        <div class="bg-danger text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center"
                            style="width: 42px; height: 42px;">
                            <i class="bi bi-x-circle-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-danger mb-1">Status Rekomendasi: TIDAK LAYAK / DITOLAK</h6>
                            <p class="text-dark mb-0 small">
                                <strong>Alasan & Penjelasan:</strong> Calon nasabah atas nama
                                <strong>{{ $customer->name }}</strong> dinyatakan <strong>TIDAK LAYAK</strong> karena belum
                                memenuhi standar minimum indikator kelayakan kredit PT FIFGROUP. Kombinasi faktor indikator
                                penilaian belum memenuhi ambang batas aman pembiayaan.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="card border-0 bg-success bg-opacity-10 rounded-3 p-4">
                    <div class="d-flex align-items-start">
                        <div class="bg-success text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center"
                            style="width: 42px; height: 42px;">
                            <i class="bi bi-check-circle-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-success mb-1">Status Rekomendasi: LAYAK (DIREKOMENDASIKAN)</h6>
                            <p class="text-dark mb-0 small">
                                <strong>Alasan & Penjelasan:</strong> Calon nasabah atas nama
                                <strong>{{ $customer->name }}</strong> telah memenuhi seluruh standar kriteria dan indikator
                                kelayakan kredit PT FIFGROUP. Profil indikator nasabah berada pada tingkat risiko yang rendah
                                dan sangat direkomendasikan untuk disetujui.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection