<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Kontrak - {{ $customer->name }} ({{ $customer->suratKontrak->nomor_kontrak ?? 'KTR' }})</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1e293b;
        }
        .contract-card {
            max-width: 850px;
            margin: 40px auto;
            background: #ffffff;
            padding: 50px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
        }
        .kop-surat {
            border-bottom: 3px double #08328c;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .kop-logo {
            max-height: 75px;
        }
        .contract-title {
            color: #08328c;
            font-weight: 800;
            letter-spacing: 1px;
        }
        .section-header {
            background-color: #eff6ff;
            color: #08328c;
            font-weight: 700;
            padding: 8px 15px;
            border-radius: 6px;
            border-left: 4px solid #08328c;
            margin-top: 25px;
            margin-bottom: 15px;
        }
        .table-info td {
            padding: 6px 12px;
        }
        .stamp-box {
            border: 2px dashed #16a34a;
            color: #16a34a;
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
            font-weight: bold;
            text-transform: uppercase;
        }
        @media print {
            body {
                background: none;
            }
            .contract-card {
                box-shadow: none;
                border: none;
                padding: 0;
                margin: 0;
                width: 100%;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="container no-print mt-4 text-center">
    <div class="d-flex justify-content-center gap-3">
        <button onclick="window.print()" class="btn btn-primary btn-lg shadow-sm rounded-pill px-4">
            <i class="bi bi-printer-fill me-2"></i> Cetak Surat Kontrak (PDF)
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary btn-lg rounded-pill px-4">
            <i class="bi bi-x-circle me-2"></i> Tutup Halaman
        </button>
    </div>
</div>

<div class="contract-card">
    <!-- KOP SURAT -->
    <div class="kop-surat d-flex justify-content-between align-items-center">
        <div>
            <img src="{{ asset('assets/logo.png') }}" alt="FIFGROUP Logo" class="kop-logo">
        </div>
        <div class="text-end">
            <h4 class="fw-bold mb-0 text-dark">PT FEDERAL INTERNASIONAL FINANCE</h4>
            <div class="text-muted small">MEMBER OF ASTRA GROUP</div>
            <div class="text-muted small">Jl. T.B. Simatupang No. 15, Cilandak, Jakarta Selatan</div>
            <div class="text-muted small">Telp: (021) 7698888 | www.fifgroup.co.id</div>
        </div>
    </div>

    <!-- JUDUL SURAT -->
    <div class="text-center mb-4">
        <h4 class="contract-title mb-1">SURAT PERJANJIAN PEMBIAYAAN KREDIT & DOKUMEN PERSETUJUAN</h4>
        <div class="fw-bold text-secondary">Nomor Kontrak: {{ $customer->suratKontrak->nomor_kontrak ?? 'KTR/FIF/2026/001' }}</div>
    </div>

    <!-- 1. IDENTITAS CALON NASABAH -->
    <div class="section-header">I. IDENTITAS CALON NASABAH</div>
    <table class="table table-borderless table-info">
        <tr>
            <td width="30%" class="fw-medium text-muted">Nomor Induk Kependudukan (NIK)</td>
            <td width="5%">:</td>
            <td class="fw-bold text-dark">{{ $customer->nik }}</td>
        </tr>
        <tr>
            <td class="fw-medium text-muted">Nama Lengkap Nasabah</td>
            <td>:</td>
            <td class="fw-bold text-dark">{{ $customer->name }}</td>
        </tr>
        <tr>
            <td class="fw-medium text-muted">Alamat Domisili</td>
            <td>:</td>
            <td>{{ $customer->address }}</td>
        </tr>
        <tr>
            <td class="fw-medium text-muted">Nomor Telepon / HP</td>
            <td>:</td>
            <td>{{ $customer->phone ?? '-' }}</td>
        </tr>
    </table>

    <!-- 2. HASIL EVALUASI SPK (BWM & TOPSIS) -->
    <div class="section-header">II. HASIL REKOMENDASI SPK METODE BWM & TOPSIS</div>
    <table class="table table-bordered text-center align-middle">
        <thead class="table-light">
            <tr>
                <th>Nilai Preferensi TOPSIS (CCi)</th>
                <th>Peringkat Kelayakan</th>
                <th>Rekomendasi Kelayakan Sistem</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="fw-bold fs-5 text-primary">{{ round($customer->topsis_score ?? 0, 4) }}</td>
                <td class="fw-bold fs-5">Peringkat {{ $customer->topsis_rank ?? '-' }}</td>
                <td>
                    @if($customer->topsis_rank <= 3)
                        <span class="badge bg-success px-3 py-2">Sangat Layak</span>
                    @elseif($customer->topsis_score >= 0.5)
                        <span class="badge bg-primary px-3 py-2">Layak</span>
                    @else
                        <span class="badge bg-danger px-3 py-2">Tidak Layak</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <!-- 3. KEPUTUSAN CAP (CREDIT APPROVAL) -->
    <div class="section-header">III. KEPUTUSAN PERSETUJUAN (CREDIT APPROVAL / CAP)</div>
    <div class="p-3 bg-light rounded-3 border d-flex justify-content-between align-items-center">
        <div>
            <div class="text-muted small">Status Persetujuan Akhir:</div>
            <div class="stamp-box">
                <i class="bi bi-check-circle-fill me-2"></i> {{ strtoupper($customer->status_persetujuan) }} / DISETUJUI CAP
            </div>
        </div>
        <div class="text-end">
            <div class="text-muted small">Tanggal Persetujuan:</div>
            <div class="fw-bold text-dark">{{ $customer->suratKontrak->tanggal_kontrak ? date('d F Y', strtotime($customer->suratKontrak->tanggal_kontrak)) : date('d F Y') }}</div>
        </div>
    </div>
    <p class="mt-3 small text-muted">
        Berdasarkan hasil evaluasi sistem multikriteria (BWM & TOPSIS) dan pemeriksaan dokumen pendukung, Pihak Manajemen FIFGROUP menyatakan bahwa permohonan kredit calon nasabah di atas telah <strong>DISETUJUI</strong> dan siap diproses ke tahap pencairan dana oleh tim Marketing (MCE).
    </p>

    <!-- 4. TANDA TANGAN PERSETUJUAN -->
    <div class="row mt-5 text-center">
        <div class="col-6">
            <p class="text-muted mb-5">Pihak Pertama (Calon Nasabah),</p>
            <div class="fw-bold text-dark mt-4"><u>{{ $customer->name }}</u></div>
            <div class="small text-muted">NIK: {{ $customer->nik }}</div>
        </div>
        <div class="col-6">
            <p class="text-muted mb-5">Pihak Kedua (CAP),</p>
            <div class="fw-bold text-dark mt-4"><u>CAP MANAGER FIFGROUP</u></div>
            <div class="small text-muted">NIK Karyawan: 19880412-CAP</div>
        </div>
    </div>

    <!-- FOOTER SURAT -->
    <div class="mt-5 pt-4 border-top text-center text-muted small">
        Dokumen ini diterbitkan secara otomatis oleh <strong>Sistem Rekomendasi Kelayakan Nasabah BWM-TOPSIS PT FIFGROUP</strong> dan sah sebagai bukti persetujuan pembiayaan.
    </div>
</div>

</body>
</html>
