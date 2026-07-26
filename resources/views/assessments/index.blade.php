@extends('layouts.app')

@section('title', 'Data Penilaian Nasabah')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clipboard2-data-fill me-2 text-primary"></i> Status Penilaian & Persetujuan Calon Nasabah</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4" width="5%">No</th>
                        <th width="15%">NIK</th>
                        <th>Nama Nasabah</th>
                        <th width="18%">Status Penilaian</th>
                        <th width="18%">Status Persetujuan</th>
                        <th class="text-center pe-4" width="24%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $key => $customer)
                    <tr>
                        <td class="ps-4 text-muted">{{ $key + 1 }}</td>
                        <td class="fw-medium text-secondary">{{ $customer->nik }}</td>
                        <td class="fw-bold">{{ $customer->name }}</td>
                        <td>
                            @if($customer->assessments->count() > 0)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> Sudah Dinilai</span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-2 rounded-pill"><i class="bi bi-exclamation-circle-fill me-1"></i> Belum Dinilai</span>
                            @endif
                        </td>
                        <td>
                            @if($customer->status_persetujuan === 'disetujui')
                                <span class="badge bg-success px-3 py-2 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> Disetujui</span>
                            @elseif($customer->status_persetujuan === 'ditolak')
                                <span class="badge bg-danger px-3 py-2 rounded-pill"><i class="bi bi-x-circle-fill me-1"></i> Ditolak</span>
                            @else
                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="bi bi-clock-history me-1"></i> Tertunda</span>
                            @endif
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-inline-flex align-items-center gap-2">
                                @if(Auth::user()->role === 'admin')
                                    <a href="{{ route('assessments.show', $customer->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                        <i class="bi bi-pencil-square me-1"></i> Nilai
                                    </a>
                                @endif

                                @if(Auth::user()->role === 'cap')
                                    <form action="{{ route('assessments.updateStatus', $customer->id) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        <select name="status_persetujuan" class="form-select form-select-sm rounded-pill border-secondary border-opacity-50" onchange="this.form.submit()">
                                            <option value="tertunda" {{ $customer->status_persetujuan == 'tertunda' ? 'selected' : '' }}>⏳ Tertunda</option>
                                            <option value="disetujui" {{ $customer->status_persetujuan == 'disetujui' ? 'selected' : '' }}>✅ Disetujui</option>
                                            <option value="ditolak" {{ $customer->status_persetujuan == 'ditolak' ? 'selected' : '' }}>❌ Ditolak</option>
                                        </select>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                                Belum ada data nasabah untuk dinilai.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
