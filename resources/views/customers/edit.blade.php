@extends('layouts.app')

@section('title', 'Ubah Data Calon Nasabah')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-4 pb-0">
        <h5 class="fw-bold mb-1 text-primary"><i class="bi bi-pencil-square me-2"></i> Ubah Data Calon Nasabah</h5>
        <p class="text-muted small">Perbarui data pribadi dan penyesuaian indikator kelayakan nasabah.</p>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('customers.update', $customer->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-card-text me-1 text-secondary"></i> Data Pribadi Nasabah</h6>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium">NIK (Nomor Induk Kependudukan) <span class="text-danger">*</span></label>
                    <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik', $customer->nik) }}" required>
                    @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $customer->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium">Nomor HP / WhatsApp <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $customer->phone) }}" required>
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium">Alamat Lengkap <span class="text-danger">*</span></label>
                    <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="1" required>{{ old('address', $customer->address) }}</textarea>
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <h6 class="fw-bold text-dark border-bottom pb-2 mt-4 mb-3"><i class="bi bi-sliders me-1 text-secondary"></i> Indikator Kelayakan Nasabah</h6>

            <div class="row">
                @foreach($criterias as $criteria)
                @php
                    $currentScore = isset($assessments[$criteria->id]) ? $assessments[$criteria->id]->value : null;
                @endphp
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium">
                        {{ $criteria->name }} <span class="text-danger">*</span>
                    </label>
                    <select name="criterias[{{ $criteria->id }}]" class="form-select @error('criterias.'.$criteria->id) is-invalid @enderror" required>
                        <option value="">-- Pilih Indikator --</option>
                        @foreach($criteria->subcriterias as $sub)
                            <option value="{{ $sub->id }}" 
                                {{ (old('criterias.'.$criteria->id) == $sub->id || (old('criterias.'.$criteria->id) === null && $currentScore == $sub->score)) ? 'selected' : '' }}>
                                {{ $sub->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('criterias.'.$criteria->id)<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('customers.index') }}" class="btn btn-secondary px-4">Batal</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle me-1"></i> Perbarui Data</button>
            </div>
        </form>
    </div>
</div>
@endsection
