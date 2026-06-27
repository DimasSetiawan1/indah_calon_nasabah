@extends('layouts.app')

@section('title', 'Data Penilaian Nasabah')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clipboard2-data-fill me-2 text-primary"></i> Status Penilaian Calon Nasabah</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" width="5%">No</th>
                        <th width="15%">NIK</th>
                        <th>Nama Nasabah</th>
                        <th width="20%">Status Penilaian</th>
                        <th class="text-center pe-4" width="15%">Aksi</th>
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
                        <td class="text-center pe-4">
                            <a href="{{ route('assessments.show', $customer->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                <i class="bi bi-pencil-square me-1"></i> Input / Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
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
