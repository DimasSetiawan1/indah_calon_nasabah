@extends('layouts.app')

@section('title', 'Data Kriteria')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Daftar Kriteria</span>
        <a href="{{ route('criterias.create') }}" class="btn btn-sm btn-primary">Tambah Kriteria</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Kriteria</th>
                    <th>Jenis</th>
                    <th>Bobot (BWM)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($criterias as $key => $criteria)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $criteria->code }}</td>
                    <td>{{ $criteria->name }}</td>
                    <td><span class="badge bg-{{ $criteria->type == 'benefit' ? 'success' : 'danger' }}">{{ ucfirst($criteria->type) }}</span></td>
                    <td>{{ $criteria->weight ? round($criteria->weight, 4) : '-' }}</td>
                    <td>
                        <a href="{{ route('criterias.edit', $criteria->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('criterias.destroy', $criteria->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus kriteria ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @if($criterias->isEmpty())
                <tr>
                    <td colspan="6" class="text-center">Belum ada data kriteria.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
