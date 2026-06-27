@extends('layouts.app')

@section('title', 'Penjabaran Hasil TOPSIS')

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
                <ul class="nav nav-tabs border-bottom-0" id="topsisTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium active text-primary" id="result-tab" data-bs-toggle="tab" data-bs-target="#result" type="button" role="tab"><i class="bi bi-trophy-fill me-1"></i> Hasil Akhir</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium text-secondary" id="matrix-x-tab" data-bs-toggle="tab" data-bs-target="#matrix-x" type="button" role="tab">Matriks X</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium text-secondary" id="matrix-r-tab" data-bs-toggle="tab" data-bs-target="#matrix-r" type="button" role="tab">Matriks R</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium text-secondary" id="matrix-v-tab" data-bs-toggle="tab" data-bs-target="#matrix-v" type="button" role="tab">Matriks V</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium text-secondary" id="ideal-tab" data-bs-toggle="tab" data-bs-target="#ideal" type="button" role="tab">A+ / A-</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-medium text-secondary" id="distance-tab" data-bs-toggle="tab" data-bs-target="#distance" type="button" role="tab">Jarak D+ / D-</button>
                    </li>
                </ul>
            </div>
            
            <div class="card-body p-0 border-top">
                <div class="tab-content" id="topsisTabsContent">
                    
                    <!-- TAB 1: HASIL AKHIR -->
                    <div class="tab-pane fade show active p-4" id="result" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">Peringkat Kelayakan Nasabah</h5>
                                <p class="text-muted small mb-0">Diurutkan berdasarkan nilai preferensi (CCi) tertinggi.</p>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover align-middle border">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" width="10%">Peringkat</th>
                                        <th width="15%">NIK</th>
                                        <th>Nama Nasabah</th>
                                        <th class="text-center" width="20%">Nilai Preferensi (CCi)</th>
                                        <th class="text-center" width="15%">Status Kelayakan</th>
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
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 2: MATRIKS X -->
                    <div class="tab-pane fade p-4" id="matrix-x" role="tabpanel">
                        <h5 class="fw-bold mb-1">Matriks Keputusan (X)</h5>
                        <p class="text-muted small mb-4">Nilai mentah penilaian masing-masing nasabah.</p>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th rowspan="2" class="align-middle">Alternatif</th>
                                        <th colspan="{{ $criterias->count() }}">Kriteria</th>
                                    </tr>
                                    <tr>
                                        @foreach($criterias as $c)
                                            <th>{{ $c->code }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customers as $customer)
                                    <tr>
                                        <td class="text-start fw-medium">{{ $customer->name }}</td>
                                        @foreach($criterias as $c)
                                            <td>{{ $X[$customer->id][$c->id] }}</td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 3: MATRIKS R -->
                    <div class="tab-pane fade p-4" id="matrix-r" role="tabpanel">
                        <h5 class="fw-bold mb-1">Matriks Normalisasi (R)</h5>
                        <p class="text-muted small mb-4">Hasil normalisasi matriks X agar satuannya setara.</p>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>Alternatif</th>
                                        @foreach($criterias as $c)
                                            <th>{{ $c->code }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customers as $customer)
                                    <tr>
                                        <td class="text-start fw-medium">{{ $customer->name }}</td>
                                        @foreach($criterias as $c)
                                            <td>{{ round($R[$customer->id][$c->id], 4) }}</td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 4: MATRIKS V -->
                    <div class="tab-pane fade p-4" id="matrix-v" role="tabpanel">
                        <h5 class="fw-bold mb-1">Matriks Normalisasi Terbobot (V)</h5>
                        <p class="text-muted small mb-4">Matriks R dikalikan dengan bobot hasil BWM.</p>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>Alternatif</th>
                                        @foreach($criterias as $c)
                                            <th>{{ $c->code }} <br><span class="text-primary small fw-normal">(w: {{ round($c->weight, 4) }})</span></th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customers as $customer)
                                    <tr>
                                        <td class="text-start fw-medium">{{ $customer->name }}</td>
                                        @foreach($criterias as $c)
                                            <td>{{ round($V[$customer->id][$c->id], 4) }}</td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 5: IDEAL SOLUTION -->
                    <div class="tab-pane fade p-4" id="ideal" role="tabpanel">
                        <h5 class="fw-bold mb-1">Solusi Ideal Positif (A+) & Negatif (A-)</h5>
                        <p class="text-muted small mb-4">Mencari nilai ekstrem berdasarkan jenis kriteria (Benefit / Cost).</p>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>Solusi Ideal</th>
                                        @foreach($criterias as $c)
                                            <th>{{ $c->code }} <br><span class="badge {{ $c->type == 'benefit' ? 'bg-success' : 'bg-danger' }} fw-normal small">{{ ucfirst($c->type) }}</span></th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-success bg-opacity-25">
                                        <td class="text-start fw-bold text-success">A+ (Positif)</td>
                                        @foreach($criterias as $c)
                                            <td class="fw-medium">{{ round($A_plus[$c->id], 4) }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="table-danger bg-opacity-25">
                                        <td class="text-start fw-bold text-danger">A- (Negatif)</td>
                                        @foreach($criterias as $c)
                                            <td class="fw-medium">{{ round($A_min[$c->id], 4) }}</td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 6: DISTANCE -->
                    <div class="tab-pane fade p-4" id="distance" role="tabpanel">
                        <h5 class="fw-bold mb-1">Jarak Solusi Ideal (D+ dan D-)</h5>
                        <p class="text-muted small mb-4">Jarak Euclidean dari masing-masing alternatif ke A+ dan A-.</p>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>Alternatif</th>
                                        <th class="text-success">Jarak Positif (D+)</th>
                                        <th class="text-danger">Jarak Negatif (D-)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customers as $customer)
                                    <tr>
                                        <td class="text-start fw-medium">{{ $customer->name }}</td>
                                        <td class="text-success fw-medium">{{ round($D_plus[$customer->id], 4) }}</td>
                                        <td class="text-danger fw-medium">{{ round($D_min[$customer->id], 4) }}</td>
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
</div>

@endif

@endsection

@push('scripts')
<script>
    // Tab switching logic via Bootstrap CSS classes if needed (handled by BS5 data-bs-toggle automatically)
    document.querySelectorAll('#topsisTabs button').forEach(button => {
        button.addEventListener('click', () => {
            document.querySelectorAll('#topsisTabs button').forEach(btn => {
                btn.classList.remove('text-primary');
                btn.classList.add('text-secondary');
            });
            button.classList.remove('text-secondary');
            button.classList.add('text-primary');
        });
    });
</script>
@endpush
