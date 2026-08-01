@extends('layouts.app')

@section('title', 'Pengaturan Bobot Penilaian')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header d-flex align-items-center bg-white border-bottom-0 pt-4 pb-0">
                    <div class="bg-primary bg-opacity-10 text-primary rounded d-flex align-items-center justify-content-center me-3"
                        style="width: 40px; height: 40px;">
                        <i class="bi bi-sliders fs-5"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Input Preferensi Indikator</h5>
                        <small class="text-muted">Tentukan tingkat kepentingan antar indikator penilaian</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div
                            class="alert alert-success border-0 bg-success bg-opacity-10 py-3 px-4 mb-4 rounded-3 d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-success fs-5 me-3"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                    @endif

                    <form action="{{ route('bwm.calculate') }}" method="POST">
                        @csrf

                        {{-- <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Indikator Utama (Terpenting)</label>
                                <select name="best_criteria" id="best_criteria"
                                    class="form-select border-primary border-opacity-50" required>
                                    <option value="">-- Pilih Indikator Utama --</option>
                                    @foreach($criterias as $c)
                                    <option value="{{ $c->id }}" {{ (old('best_criteria')==$c->id || (old('best_criteria')
                                        === null && \Illuminate\Support\Str::contains(strtolower($c->name), 'riwayat
                                        kredit'))) ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="form-text text-muted small"><i class="bi bi-info-circle"></i> Indikator yang
                                    paling menentukan kelayakan.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Indikator Pendukung (Terendah)</label>
                                <select name="worst_criteria" id="worst_criteria"
                                    class="form-select border-danger border-opacity-50" required>
                                    <option value="">-- Pilih Indikator Pendukung --</option>
                                    @foreach($criterias as $c)
                                    <option value="{{ $c->id }}" {{ (old('worst_criteria')==$c->id || (old('worst_criteria')
                                        === null && \Illuminate\Support\Str::contains(strtolower($c->name), 'jaminan
                                        kendaraan'))) ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="form-text text-muted small"><i class="bi bi-info-circle"></i> Indikator dengan
                                    tingkat keutamaan terendah.</div>
                            </div>
                        </div> --}}

                        {{--
                        <hr class="border-light my-4"> --}}

                        <div class="card border-0 bg-primary bg-opacity-10 rounded-3 mb-4">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="bi bi-lightbulb-fill me-2"></i> Panduan Pengisian Skala Perbandingan (1 - 9):
                                </h6>
                                <div class="row g-2 text-dark small">
                                    <div class="col-md-4">
                                        <div class="bg-white p-2 rounded border shadow-sm h-100">
                                            <span class="badge bg-primary me-1">1</span> <strong>Sama Penting</strong>
                                            <div class="text-muted" style="font-size: 0.75rem;">Keutamaan kedua indikator
                                                setara.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="bg-white p-2 rounded border shadow-sm h-100">
                                            <span class="badge bg-primary me-1">3</span> <strong>Sedikit Lebih
                                                Penting</strong>
                                            <div class="text-muted" style="font-size: 0.75rem;">Sedikit lebih diutamakan.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="bg-white p-2 rounded border shadow-sm h-100">
                                            <span class="badge bg-primary me-1">5</span> <strong>Lebih Penting</strong>
                                            <div class="text-muted" style="font-size: 0.75rem;">Jelas lebih diutamakan.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="bg-white p-2 rounded border shadow-sm h-100">
                                            <span class="badge bg-primary me-1">7</span> <strong>Sangat Penting</strong>
                                            <div class="text-muted" style="font-size: 0.75rem;">Sangat mendominasi
                                                keutamaan.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="bg-white p-2 rounded border shadow-sm h-100">
                                            <span class="badge bg-primary me-1">9</span> <strong>Mutlak Lebih
                                                Penting</strong>
                                            <div class="text-muted" style="font-size: 0.75rem;">Pengaruh mutlak & tertinggi.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="bg-white p-2 rounded border shadow-sm h-100">
                                            <span class="badge bg-secondary me-1">2, 4, 6, 8</span> <strong>Nilai
                                                Antara</strong>
                                            <div class="text-muted" style="font-size: 0.75rem;">Nilai tengah perbandingan.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="34%">Indikator Penilaian</th>
                                        <th width="33%" class="text-center" id="th_best_header">Riwayat Kredit ke Indikator
                                            Lain</th>
                                        <th width="33%" class="text-center" id="th_worst_header">Indikator Lain ke Jaminan
                                            Kendaraan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($criterias as $c)
                                        <tr>
                                            <td class="fw-medium text-secondary">
                                                {{ $c->name }}
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="bo[{{ $c->id }}]" id="bo_{{ $c->id }}"
                                                        class="form-control text-center fw-bold bo-input" min="1" max="9"
                                                        required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="ow[{{ $c->id }}]" id="ow_{{ $c->id }}"
                                                        class="form-control text-center fw-bold ow-input" min="1" max="9"
                                                        required>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary py-2 fw-bold">
                                <i class="bi bi-sliders me-2"></i> Hitung Bobot Penilaian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const bestSelect = document.getElementById('best_criteria');
            const worstSelect = document.getElementById('worst_criteria');
            const headerBestEl = document.getElementById('th_best_header');
            const headerWorstEl = document.getElementById('th_worst_header');

            function updateHeaders() {
                const bestText = bestSelect.options[bestSelect.selectedIndex]?.text;
                const worstText = worstSelect.options[worstSelect.selectedIndex]?.text;

                if (headerBestEl) {
                    headerBestEl.textContent = (bestSelect.value && !bestText.includes('--'))
                        ? (bestText + ' ke Indikator Lain')
                        : 'Riwayat Kredit ke Indikator Lain';
                }
                if (headerWorstEl) {
                    headerWorstEl.textContent = (worstSelect.value && !worstText.includes('--'))
                        ? ('Indikator Lain ke ' + worstText)
                        : 'Indikator Lain ke Jaminan Kendaraan';
                }
            }

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
                    if (boBest) {
                        boBest.value = 1;
                        boBest.readOnly = true;
                        boBest.classList.add('bg-light'); // visual cue that it's locked
                    }
                }

                if (worstId) {
                    const owWorst = document.getElementById('ow_' + worstId);
                    if (owWorst) {
                        owWorst.value = 1;
                        owWorst.readOnly = true;
                        owWorst.classList.add('bg-light'); // visual cue that it's locked
                    }
                }

                updateHeaders();
            }

            bestSelect.addEventListener('change', updateInputs);
            worstSelect.addEventListener('change', updateInputs);

            // Initial call on load
            updateInputs();
        });
    </script>
@endpush