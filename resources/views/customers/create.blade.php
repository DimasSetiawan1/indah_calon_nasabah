@extends('layouts.app')

@section('title', 'Tambah Calon Nasabah')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-4 pb-0">
        <h5 class="fw-bold mb-1 text-primary"><i class="bi bi-person-plus-fill me-2"></i> Tambah Calon Nasabah</h5>
        <p class="text-muted small">Lengkapi data pribadi dan pilih indikator kelayakan nasabah.</p>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('customers.store') }}" method="POST">
            @csrf
            
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-card-text me-1 text-secondary"></i> Data Pribadi Nasabah</h6>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium">NIK (Nomor Induk Kependudukan) <span class="text-danger">*</span></label>
                    <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik') }}" placeholder="16 Digit NIK" required>
                    @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Nama Lengkap Nasabah" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium">Nomor HP / WhatsApp <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="Contoh: 08123456789" required>
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium">Alamat Lengkap <span class="text-danger">*</span></label>
                    <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="1" placeholder="Alamat Domisili" required>{{ old('address') }}</textarea>
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <h6 class="fw-bold text-dark border-bottom pb-2 mt-4 mb-3"><i class="bi bi-box me-1 text-secondary"></i> Data Pengajuan Pinjaman</h6>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium">Umur Calon Nasabah <span class="text-danger">*</span></label>
                    <input type="number" name="umur" class="form-control @error('umur') is-invalid @enderror" value="{{ old('umur') }}" placeholder="Contoh: 25" required>
                    @error('umur')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium">Kategori Nasabah <span class="text-danger">*</span></label>
                    <select name="kategori_persentase" class="form-select @error('kategori_persentase') is-invalid @enderror" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="New Customer|50" {{ old('kategori_persentase') == 'New Customer|50' ? 'selected' : '' }}>New Customer (50%)</option>
                        <option value="New Customer|55" {{ old('kategori_persentase') == 'New Customer|55' ? 'selected' : '' }}>New Customer (55%)</option>
                        <option value="New Customer|60" {{ old('kategori_persentase') == 'New Customer|60' ? 'selected' : '' }}>New Customer (60%)</option>
                        <option value="New Customer|65" {{ old('kategori_persentase') == 'New Customer|65' ? 'selected' : '' }}>New Customer (65%)</option>
                        <option value="Medium|75" {{ old('kategori_persentase') == 'Medium|75' ? 'selected' : '' }}>Medium (75%)</option>
                        <option value="Good Loyal|85" {{ old('kategori_persentase') == 'Good Loyal|85' ? 'selected' : '' }}>Good Loyal (85%)</option>
                    </select>
                    @error('kategori_persentase')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium">Nama Barang <span class="text-danger">*</span></label>
                    <input type="text" name="nama_barang" class="form-control @error('nama_barang') is-invalid @enderror" value="{{ old('nama_barang') }}" placeholder="Contoh: Honda Vario 150" required>
                    @error('nama_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium">Nilai Barang (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="nilai_barang" class="form-control @error('nilai_barang') is-invalid @enderror" value="{{ old('nilai_barang') }}" placeholder="Contoh: 15000000" required>
                    @error('nilai_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <h6 class="fw-bold text-dark border-bottom pb-2 mt-4 mb-3"><i class="bi bi-sliders me-1 text-secondary"></i> Indikator Kelayakan Nasabah</h6>

            <div class="row">
                @foreach($criterias as $criteria)
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-medium">
                        {{ $criteria->name }} <span class="text-danger">*</span>
                    </label>
                    <select name="criterias[{{ $criteria->id }}]" class="form-select @error('criterias.'.$criteria->id) is-invalid @enderror" required>
                        <option value="">-- Pilih Indikator --</option>
                        @foreach($criteria->subcriterias as $sub)
                            <option value="{{ $sub->id }}" {{ old('criterias.'.$criteria->id) == $sub->id ? 'selected' : '' }}>
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
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Simpan Data</button>
            </div>
        </form>
    </div>
</div>
@endsection
