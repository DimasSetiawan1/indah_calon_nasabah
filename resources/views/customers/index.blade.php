@extends('layouts.app')

@section('title', 'Data Nasabah')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people-fill me-2 text-primary"></i> Daftar Calon Nasabah</span>
        <a href="{{ route('customers.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah Nasabah
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" width="5%">No</th>
                        <th width="15%">NIK</th>
                        <th>Nama Lengkap</th>
                        <th width="25%">Alamat</th>
                        <th width="15%">No. HP</th>
                        <th class="text-center pe-4" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $key => $customer)
                    <tr>
                        <td class="ps-4 text-muted">{{ $key + 1 }}</td>
                        <td class="fw-medium text-secondary">{{ $customer->nik }}</td>
                        <td class="fw-bold">{{ $customer->name }}</td>
                        <td class="text-muted small">{{ \Illuminate\Support\Str::limit($customer->address, 40) }}</td>
                        <td>{{ $customer->phone ?? '-' }}</td>
                        <td class="text-center pe-4">
                            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data nasabah ini?')">
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
                                <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                                Belum ada data nasabah.
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
