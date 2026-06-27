@extends('layouts.app')

@section('title', 'Perhitungan Best Worst Method (BWM)')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">Input Preferensi BWM</div>
            <div class="card-body">
                <form action="{{ route('bwm.calculate') }}" method="POST">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kriteria Terbaik (Best)</label>
                            <select name="best_criteria" id="best_criteria" class="form-select" required>
                                <option value="">-- Pilih Best Criteria --</option>
                                @foreach($criterias as $c)
                                    <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kriteria Terburuk (Worst)</label>
                            <select name="worst_criteria" id="worst_criteria" class="form-select" required>
                                <option value="">-- Pilih Worst Criteria --</option>
                                @foreach($criterias as $c)
                                    <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Kriteria</th>
                                    <th>Best-to-Others (BO) <br><small class="text-muted">Seberapa penting Best dibanding kriteria ini? (1-9)</small></th>
                                    <th>Others-to-Worst (OW) <br><small class="text-muted">Seberapa penting kriteria ini dibanding Worst? (1-9)</small></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($criterias as $c)
                                <tr>
                                    <td>{{ $c->code }} - {{ $c->name }}</td>
                                    <td>
                                        <input type="number" name="bo[{{ $c->id }}]" id="bo_{{ $c->id }}" class="form-control bo-input" min="1" max="9" required>
                                    </td>
                                    <td>
                                        <input type="number" name="ow[{{ $c->id }}]" id="ow_{{ $c->id }}" class="form-control ow-input" min="1" max="9" required>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <button type="submit" class="btn btn-success mt-3">Hitung Bobot BWM</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">Hasil Bobot Kriteria</div>
            <div class="card-body">
                <ul class="list-group">
                    @foreach($criterias as $c)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $c->code }}
                        <span class="badge bg-primary rounded-pill">{{ $c->weight ? round($c->weight, 4) : 'Belum dihitung' }}</span>
                    </li>
                    @endforeach
                </ul>
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
            
            // Reset all
            document.querySelectorAll('.bo-input, .ow-input').forEach(el => {
                el.readOnly = false;
            });
            
            if (bestId) {
                const boBest = document.getElementById('bo_' + bestId);
                if(boBest) {
                    boBest.value = 1;
                    boBest.readOnly = true;
                }
            }
            
            if (worstId) {
                const owWorst = document.getElementById('ow_' + worstId);
                if(owWorst) {
                    owWorst.value = 1;
                    owWorst.readOnly = true;
                }
                
                // Optional: The Best to Worst should be consistent, e.g. bo[worst] == ow[best]
            }
        }

        bestSelect.addEventListener('change', updateInputs);
        worstSelect.addEventListener('change', updateInputs);
    });
</script>
@endpush
