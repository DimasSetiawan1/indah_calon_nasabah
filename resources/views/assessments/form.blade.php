@extends('layouts.app')

@section('title', 'Input Nilai Kriteria')

@section('content')
<div class="card">
    <div class="card-header">
        Nilai untuk Nasabah: <strong>{{ $customer->name }} ({{ $customer->nik }})</strong>
    </div>
    <div class="card-body">
        <form action="{{ route('assessments.store') }}" method="POST">
            @csrf
            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
            
            @foreach($criterias as $criteria)
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label">{{ $criteria->code }} - {{ $criteria->name }} ({{ ucfirst($criteria->type) }})</label>
                <div class="col-sm-9">
                    <input type="number" step="any" name="values[{{ $criteria->id }}]" class="form-control" value="{{ $assessments[$criteria->id] ?? '' }}" required>
                </div>
            </div>
            @endforeach
            
            @if($criterias->isEmpty())
                <div class="alert alert-warning">Data Kriteria kosong. Silakan tambah kriteria terlebih dahulu.</div>
            @else
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Simpan Penilaian</button>
                    <a href="{{ route('assessments.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection
