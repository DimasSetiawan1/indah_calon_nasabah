@extends('layouts.app')

@section('title', 'Analisis BWM (Best Worst Method)')

@section('content')
<div class="row">
    <!-- Left Column: Form Input -->
    <div class="col-xl-7 col-lg-12">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center bg-white border-bottom-0 pt-4 pb-0">
                <div class="bg-primary bg-opacity-10 text-primary rounded d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                    <i class="bi bi-sliders fs-5"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">Input Preferensi Kriteria</h5>
                    <small class="text-muted">Tentukan tingkat kepentingan antar kriteria</small>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('bwm.calculate') }}" method="POST">
                    @csrf
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Kriteria Terbaik (Best)</label>
                            <select name="best_criteria" id="best_criteria" class="form-select border-primary border-opacity-50" required>
                                <option value="">-- Pilih Best Criteria --</option>
                                @foreach($criterias as $c)
                                    <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted small"><i class="bi bi-info-circle"></i> Kriteria yang paling penting.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Kriteria Terburuk (Worst)</label>
                            <select name="worst_criteria" id="worst_criteria" class="form-select border-danger border-opacity-50" required>
                                <option value="">-- Pilih Worst Criteria --</option>
                                @foreach($criterias as $c)
                                    <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted small"><i class="bi bi-info-circle"></i> Kriteria yang paling tidak penting.</div>
                        </div>
                    </div>

                    <hr class="border-light my-4">
                    
                    <div class="alert alert-info border-0 bg-info bg-opacity-10 d-flex mb-4">
                        <i class="bi bi-lightbulb-fill text-info fs-4 me-3"></i>
                        <div class="small">
                            <strong>Cara Mengisi Nilai (Skala 1-9):</strong><br>
                            1 = Sama pentingnya &nbsp; | &nbsp; 3 = Sedikit lebih penting &nbsp; | &nbsp; 5 = Lebih penting &nbsp; | &nbsp; 7 = Sangat penting &nbsp; | &nbsp; 9 = Mutlak lebih penting.<br>
                            <em>(Gunakan angka genap 2,4,6,8 sebagai nilai tengah)</em>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th width="40%">Kriteria</th>
                                    <th width="30%">Best-to-Others (BO)</th>
                                    <th width="30%">Others-to-Worst (OW)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($criterias as $c)
                                <tr>
                                    <td class="fw-medium text-secondary">
                                        <span class="badge bg-secondary me-2">{{ $c->code }}</span>
                                        {{ $c->name }}
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="bo[{{ $c->id }}]" id="bo_{{ $c->id }}" class="form-control text-center fw-bold bo-input" min="1" max="9" required>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="ow[{{ $c->id }}]" id="ow_{{ $c->id }}" class="form-control text-center fw-bold ow-input" min="1" max="9" required>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary py-2 fw-bold">
                            <i class="bi bi-calculator me-2"></i> Proses Perhitungan BWM
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Right Column: Results -->
    <div class="col-xl-5 col-lg-12">
        <div class="card shadow-sm border-0 bg-white">
            <div class="card-header bg-white border-0 pt-4 pb-2">
                <h5 class="mb-0 fw-bold"><i class="bi bi-pie-chart-fill text-success me-2"></i> Hasil Bobot Kriteria</h5>
            </div>
            <div class="card-body">
                @php
                    $isCalculated = $criterias->every(function($c) { return $c->weight !== null; });
                @endphp
                
                @if(session('success'))
                    <div class="alert alert-success border-0 bg-success bg-opacity-10 py-2 px-3 mb-4 small rounded-3">
                        <i class="bi bi-check-circle-fill text-success me-2"></i> {{ session('success') }}
                    </div>
                @endif
                
                @if($isCalculated)
                    <div class="mb-4">
                        @foreach($criterias as $c)
                            @php
                                $percent = round($c->weight * 100, 1);
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-medium text-dark small"><span class="badge bg-light text-dark border me-1">{{ $c->code }}</span> {{ $c->name }}</span>
                                    <span class="fw-bold text-primary">{{ round($c->weight, 4) }} ({{ $percent }}%)</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="card bg-light border-0 rounded-3 p-3">
                        <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-info-circle-fill text-primary me-2"></i> Interpretasi Hasil:</h6>
                        <p class="text-muted small mb-0">
                            Bobot ini mencerminkan seberapa penting pengaruh kriteria tersebut dalam menentukan kelayakan calon nasabah. Semakin panjang bar biru, semakin besar dampaknya pada penilaian akhir (TOPSIS). Total dari seluruh bobot ini bernilai mutlak 1 (100%).
                        </p>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center p-4 mb-3">
                            <i class="bi bi-bar-chart text-secondary fs-1"></i>
                        </div>
                        <h6 class="fw-bold">Belum Ada Hasil</h6>
                        <p class="small mb-0">Silakan input preferensi dan klik tombol proses perhitungan di samping.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bestSelect = document.getElementById('best_criteria');
        const worstSelect = document.getElementById('worst_criteria');
        
        function updateInputs() {
            const bestId = bestSelect.value;
            const worstId = worstSelect.value;
            
            // Reset all input fields
            document.querySelectorAll('.bo-input, .ow-input').forEach(el => {
                el.readOnly = false;
                el.classList.remove('bg-light');
            });
            
            if (bestId) {
                const boBest = document.getElementById('bo_' + bestId);
                if(boBest) {
                    boBest.value = 1;
                    boBest.readOnly = true;
                    boBest.classList.add('bg-light'); // visual cue that it's locked
                }
            }
            
            if (worstId) {
                const owWorst = document.getElementById('ow_' + worstId);
                if(owWorst) {
                    owWorst.value = 1;
                    owWorst.readOnly = true;
                    owWorst.classList.add('bg-light'); // visual cue that it's locked
                }
            }
        }

        bestSelect.addEventListener('change', updateInputs);
        worstSelect.addEventListener('change', updateInputs);
    });
</script>
@endpush
