@extends('layouts.app')

@section('title', 'Hasil Perhitungan TOPSIS')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Hasil Perankingan Calon Nasabah</span>
        <form action="{{ route('topsis.calculate') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm btn-success">
                <i class="bi bi-arrow-clockwise"></i> Hitung / Update Perankingan
            </button>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Peringkat</th>
                        <th>NIK</th>
                        <th>Nama Nasabah</th>
                        <th>Nilai Preferensi (CCi)</th>
                        <th>Status Kelayakan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                    @if($customer->topsis_rank)
                    <tr>
                        <td class="fw-bold fs-5">{{ $customer->topsis_rank }}</td>
                        <td>{{ $customer->nik }}</td>
                        <td>{{ $customer->name }}</td>
                        <td>{{ round($customer->topsis_score, 4) }}</td>
                        <td>
                            @if($customer->topsis_rank <= 3)
                                <span class="badge bg-success">Sangat Layak</span>
                            @elseif($customer->topsis_score >= 0.5)
                                <span class="badge bg-primary">Layak</span>
                            @else
                                <span class="badge bg-danger">Tidak Layak</span>
                            @endif
                        </td>
                    </tr>
                    @endif
                    @endforeach
                    
                    @if($customers->whereNotNull('topsis_rank')->isEmpty())
                    <tr>
                        <td colspan="5" class="text-center text-muted">Belum ada hasil perankingan. Silakan tekan tombol hitung.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="alert alert-info mt-3">
            <i class="bi bi-info-circle"></i> Catatan: Semakin besar nilai CCi, semakin layak calon nasabah tersebut (berada di peringkat teratas).
        </div>
    </div>
</div>
@endsection
