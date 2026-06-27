@extends('layouts.app')

@section('title', 'Data Kriteria')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-task me-2 text-primary"></i> Daftar Kriteria Penilaian</span>
        <a href="{{ route('criterias.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kriteria
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" width="5%">No</th>
                        <th width="15%">Kode</th>
                        <th>Nama Kriteria</th>
                        <th width="15%">Jenis</th>
                        <th width="15%">Bobot (BWM)</th>
                        <th class="text-center pe-4" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($criterias as $key => $criteria)
                    <tr>
                        <td class="ps-4">{{ $key + 1 }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $criteria->code }}</span></td>
                        <td class="fw-medium">{{ $criteria->name }}</td>
                        <td>
                            @if($criteria->type == 'benefit')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1"><i class="bi bi-arrow-up-circle me-1"></i> Benefit</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1"><i class="bi bi-arrow-down-circle me-1"></i> Cost</span>
                            @endif
                        </td>
                        <td>
                            @if($criteria->weight)
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-primary me-2">{{ round($criteria->weight, 4) }}</span>
                                </div>
                            @else
                                <span class="text-muted small fst-italic">Belum dihitung</span>
                            @endif
                        </td>
                        <td class="text-center pe-4">
                            <a href="{{ route('criterias.edit', $criteria->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('criterias.destroy', $criteria->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin menghapus kriteria ini? Data penilaian terkait juga akan terhapus.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Belum ada data kriteria.
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
