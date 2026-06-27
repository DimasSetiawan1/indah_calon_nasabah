@extends('layouts.app')

@section('title', 'Data Penilaian Nasabah')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIK</th>
                        <th>Nama Nasabah</th>
                        <th>Status Penilaian</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $key => $customer)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $customer->nik }}</td>
                        <td>{{ $customer->name }}</td>
                        <td>
                            @if($customer->assessments->count() > 0)
                                <span class="badge bg-success">Sudah Dinilai</span>
                            @else
                                <span class="badge bg-warning">Belum Dinilai</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('assessments.show', $customer->id) }}" class="btn btn-sm btn-info text-white">Input / Edit Nilai</a>
                        </td>
                    </tr>
                    @endforeach
                    @if($customers->isEmpty())
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data nasabah.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
