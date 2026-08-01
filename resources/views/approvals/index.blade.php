@extends('layouts.app')

@section('title', 'Halaman Persetujuan CAP')

@section('content')
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-white text-primary p-3 me-3 d-flex align-items-center justify-content-center"
                        style="width: 56px; height: 56px;">
                        <i class="bi bi-person-check-fill fs-3"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-white-50 small">Total Pengajuan Nasabah</h6>
                        <h3 class="fw-bold mb-0">{{ $customers->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm rounded-4 bg-success text-white p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-white text-success p-3 me-3 d-flex align-items-center justify-content-center"
                        style="width: 56px; height: 56px;">
                        <i class="bi bi-check-circle-fill fs-3"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-white-50 small">Disetujui</h6>
                        <h3 class="fw-bold mb-0">{{ $customers->where('status_persetujuan', 'disetujui')->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm rounded-4 bg-warning text-dark p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-white text-warning p-3 me-3 d-flex align-items-center justify-content-center"
                        style="width: 56px; height: 56px;">
                        <i class="bi bi-clock-history fs-3"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted small">Menunggu Persetujuan (Tertunda)</h6>
                        <h3 class="fw-bold mb-0">{{ $customers->where('status_persetujuan', 'tertunda')->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-file-earmark-check-fill text-primary me-2"></i> Lembar
                    Persetujuan CAP</h5>
                <p class="text-muted small mb-0">Halaman khusus peninjauan dan persetujuan pengajuan kelayakan nasabah.</p>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" width="10%">Peringkat</th>
                            <th>Nama Nasabah</th>
                            <th width="20%" class="text-center">Status Kelayakan</th>
                            <th width="20%" class="text-center">Status Persetujuan</th>
                            <th width="26%" class="text-center pe-4">Aksi / Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td class="ps-4">
                                    @if($customer->topsis_rank == 1)
                                        <span class="badge bg-warning text-dark rounded-circle p-2 shadow-sm" title="Peringkat 1"><i
                                                class="bi bi-trophy-fill"></i></span>
                                    @elseif($customer->topsis_rank == 2)
                                        <span class="badge bg-secondary rounded-circle p-2 shadow-sm" title="Peringkat 2"><i
                                                class="bi bi-award-fill"></i></span>
                                    @elseif($customer->topsis_rank == 3)
                                        <span class="badge text-white rounded-circle p-2 shadow-sm"
                                            style="background-color: #cd7f32;" title="Peringkat 3"><i
                                                class="bi bi-award-fill"></i></span>
                                    @else
                                        <span class="fw-bold text-muted ps-2">{{ $customer->topsis_rank ?? '-' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $customer->name }}</div>
                                    <div class="text-muted small"><i class="bi bi-telephone me-1"></i>
                                        {{ $customer->phone ?? '-' }}</div>
                                </td>
                                <td class="text-center">
                                    @if($customer->topsis_rank !== null && $customer->topsis_rank <= 3)
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">Sangat
                                            Layak</span>
                                    @elseif($customer->topsis_score >= 0.5)
                                        <span
                                            class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill">Layak</span>
                                    @else
                                        <span
                                            class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill">Tidak
                                            Layak</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($customer->status_persetujuan === 'disetujui')
                                        <span class="badge bg-success px-3 py-2 rounded-pill"><i
                                                class="bi bi-check-circle-fill me-1"></i> Disetujui</span>
                                    @elseif($customer->status_persetujuan === 'ditolak')
                                        <span class="badge bg-danger px-3 py-2 rounded-pill"><i
                                                class="bi bi-x-circle-fill me-1"></i> Ditolak</span>
                                    @else
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i
                                                class="bi bi-clock-history me-1"></i> Tertunda</span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="{{ route('customers.detail', $customer->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 shadow-sm" title="Detail Hasil">
                                            <i class="bi bi-eye-fill"></i> Detail
                                        </a>

                                        @if(Auth::user()->role === 'cap')
                                            @if($customer->status_persetujuan === 'tertunda')
                                                <form action="{{ route('approvals.approve', $customer->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm"
                                                        onclick="return confirm('Apakah Anda yakin ingin MENYETUJUI pengajuan {{ $customer->name }}?')">
                                                        <i class="bi bi-check-lg me-1"></i> Setujui
                                                    </button>
                                                </form>
                                                <form action="{{ route('approvals.reject', $customer->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                                        onclick="return confirm('Apakah Anda yakin ingin MENOLAK pengajuan {{ $customer->name }}?')">
                                                        <i class="bi bi-x-lg me-1"></i> Tolak
                                                    </button>
                                                </form>
                                            @elseif($customer->status_persetujuan === 'disetujui')
                                                <span class="badge bg-success bg-opacity-10 text-success border px-3 py-2 rounded-pill"><i
                                                        class="bi bi-lock-fill me-1"></i> Disetujui</span>
                                            @elseif($customer->status_persetujuan === 'ditolak')
                                                <span
                                                    class="badge bg-secondary bg-opacity-10 text-secondary border px-3 py-2 rounded-pill"><i
                                                        class="bi bi-lock-fill me-1"></i> Ditolak</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada pengajuan calon nasabah.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection