<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SuratKontrak;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index()
    {
        $customers = Customer::with('suratKontrak')
            ->orderBy('topsis_rank', 'asc')
            ->get();

        return view('approvals.index', compact('customers'));
    }

    public function approve($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->update(['status_persetujuan' => 'disetujui']);

        // Generate / Update Surat Kontrak otomatis
        $nomorKontrak = 'KTR/FIF/' . date('Y') . '/' . str_pad($customer->id, 4, '0', STR_PAD_LEFT);

        SuratKontrak::updateOrCreate(
            ['nasabah_id' => $customer->id],
            [
                'nomor_kontrak' => $nomorKontrak,
                'status' => 'disetujui_cap',
                'tanggal_kontrak' => now(),
                'keterangan' => 'Disetujui oleh CAP. Siap diterbitkan dan dicairkan.',
            ]
        );

        return redirect()->back()->with('success', 'Nasabah ' . $customer->name . ' berhasil DISETUJUI dan Surat Kontrak diterbitkan.');
    }

    public function reject($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->update(['status_persetujuan' => 'ditolak']);

        // Hapus Surat Kontrak jika ada
        SuratKontrak::where('nasabah_id', $customer->id)->delete();

        return redirect()->back()->with('success', 'Pengajuan nasabah ' . $customer->name . ' telah DITOLAK.');
    }

    public function showContract($id)
    {
        $customer = Customer::with('suratKontrak')->findOrFail($id);

        if (!$customer->suratKontrak) {
            // Jika belum ada surat kontrak, buatkan secara otomatis
            $nomorKontrak = 'KTR/FIF/' . date('Y') . '/' . str_pad($customer->id, 4, '0', STR_PAD_LEFT);
            $contract = SuratKontrak::create([
                'nasabah_id' => $customer->id,
                'nomor_kontrak' => $nomorKontrak,
                'status' => 'disetujui_cap',
                'tanggal_kontrak' => now(),
                'keterangan' => 'Disetujui oleh CAP.',
            ]);
            $customer->load('suratKontrak');
        }

        return view('approvals.contract', compact('customer'));
    }
}
