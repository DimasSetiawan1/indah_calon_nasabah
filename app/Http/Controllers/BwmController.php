<?php

namespace App\Http\Controllers;

use App\Models\Criteria;
use Illuminate\Http\Request;

class BwmController extends Controller
{
    public function index()
    {
        $criterias = Criteria::all();
        return view('bwm.index', compact('criterias'));
    }

    public function calculate(Request $request)
    {
        $criterias = Criteria::all();
        if ($criterias->count() < 3) {
            return redirect()->back()->with('error', 'Minimal 3 kriteria diperlukan untuk BWM.');
        }

        $best_id = $request->best_criteria;
        $worst_id = $request->worst_criteria;
        
        $bo = $request->bo; // Array of Best-to-Others
        $ow = $request->ow; // Array of Others-to-Worst

        // Validasi input
        if (!$best_id || !$worst_id) {
            return redirect()->back()->with('error', 'Kriteria Best dan Worst harus dipilih.');
        }
        
        // BO untuk best harus 1, OW untuk worst harus 1
        $bo[$best_id] = 1;
        $ow[$worst_id] = 1;

        // Aproksimasi Analitik BWM (karena keterbatasan Solver LP di PHP)
        // 1. Hitung bobot berdasarkan BO (Best-to-Others) -> w_j = (1/BO_j) / sum(1/BO_k)
        // 2. Hitung bobot berdasarkan OW (Others-to-Worst) -> w_j = OW_j / sum(OW_k)
        // 3. Rata-rata dari keduanya.
        
        $sum_inv_bo = 0;
        $sum_ow = 0;

        foreach ($criterias as $c) {
            $sum_inv_bo += 1 / max(1, $bo[$c->id]);
            $sum_ow += max(1, $ow[$c->id]);
        }

        $weights = [];
        foreach ($criterias as $c) {
            $w_bo = (1 / max(1, $bo[$c->id])) / $sum_inv_bo;
            $w_ow = max(1, $ow[$c->id]) / $sum_ow;
            
            // Average weight
            $w_avg = ($w_bo + $w_ow) / 2;
            $weights[$c->id] = $w_avg;
        }

        // Normalisasi akhir untuk memastikan jumlah = 1
        $total_weight = array_sum($weights);
        foreach ($criterias as $c) {
            $final_weight = $weights[$c->id] / $total_weight;
            
            // Simpan ke database
            $c->update(['weight' => $final_weight]);
        }

        // Estimasi Konsistensi Xi (Ksi)
        $xi_bo = 0;
        $xi_ow = 0;
        $w_best = $weights[$best_id] / $total_weight;
        $w_worst = $weights[$worst_id] / $total_weight;

        foreach ($criterias as $c) {
            $w_j = $weights[$c->id] / $total_weight;
            $xi_bo = max($xi_bo, abs($w_best - $bo[$c->id] * $w_j));
            $xi_ow = max($xi_ow, abs($w_j - $ow[$c->id] * $w_worst));
        }
        $consistency_ratio = max($xi_bo, $xi_ow);

        return redirect()->route('bwm.index')->with('success', 'Perhitungan BWM selesai. Konsistensi (Xi) = ' . round($consistency_ratio, 4));
    }
}
