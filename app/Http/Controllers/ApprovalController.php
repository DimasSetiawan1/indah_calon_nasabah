<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index()
    {
        $query = Customer::whereNotNull('topsis_score')
            ->whereNotNull('topsis_rank');

        // Untuk MCE dan CAP, hanya tampilkan nasabah yang LAYAK (rank <= 3 atau score >= 0.5)
        if (in_array(auth()->user()->role, ['mce', 'cap'])) {
            $query->where(function ($q) {
                $q->where('topsis_rank', '<=', 3)
                  ->orWhere('topsis_score', '>=', 0.5);
            });
        }

        $customers = $query->orderBy('topsis_rank', 'asc')->get();

        return view('approvals.index', compact('customers'));
    }

    public function approve($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->update(['status_persetujuan' => 'disetujui']);

        return redirect()->back()->with('success', 'Pengajuan nasabah ' . $customer->name . ' berhasil DISETUJUI.');
    }

    public function reject($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->update(['status_persetujuan' => 'ditolak']);

        return redirect()->back()->with('success', 'Pengajuan nasabah ' . $customer->name . ' telah DITOLAK.');
    }
}
