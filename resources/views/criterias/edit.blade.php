@extends('layouts.app')

@section('title', 'Edit Kriteria')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('criterias.update', $criteria->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Kode Kriteria</label>
                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $criteria->code) }}" required>
                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Kriteria</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $criteria->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Jenis Kriteria</label>
                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="benefit" {{ old('type', $criteria->type) == 'benefit' ? 'selected' : '' }}>Benefit</option>
                    <option value="cost" {{ old('type', $criteria->type) == 'cost' ? 'selected' : '' }}>Cost</option>
                </select>
                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary">Perbarui</button>
            <a href="{{ route('criterias.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection
