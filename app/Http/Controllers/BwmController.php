<?php

namespace App\Http\Controllers;

use App\Models\Criteria;
use App\Models\Customer;
use App\Http\Controllers\TopsisController;
use Illuminate\Http\Request;

class BwmController extends Controller
{
    public function index()
    {
        $criterias = Criteria::all();

        // Default fallback BO & OW values matching paper matrix
        $defaultBO = [];
        $defaultOW = [];
        foreach ($criterias as $c) {
            if ($c->code === 'C1') {
                $defaultBO[$c->id] = 2;
                $defaultOW[$c->id] = 4;
            } elseif ($c->code === 'C2') {
                $defaultBO[$c->id] = 3;
                $defaultOW[$c->id] = 3;
            } elseif ($c->code === 'C3') {
                $defaultBO[$c->id] = 1;
                $defaultOW[$c->id] = 5;
            } elseif ($c->code === 'C4') {
                $defaultBO[$c->id] = 4;
                $defaultOW[$c->id] = 2;
            } elseif ($c->code === 'C5') {
                $defaultBO[$c->id] = 5;
                $defaultOW[$c->id] = 1;
            } else {
                $defaultBO[$c->id] = 1;
                $defaultOW[$c->id] = 1;
            }
        }

        return view('bwm.index', compact('criterias', 'defaultBO', 'defaultOW'));
    }

    public function calculate(Request $request)
    {
        $criterias = Criteria::all();
        if ($criterias->count() < 3) {
            return redirect()->back()->with('error', 'Minimal 3 kriteria diperlukan untuk BWM.');
        }

        $bestC = Criteria::where('code', 'C3')->first() ?? $criterias->first();
        $worstC = Criteria::where('code', 'C5')->first() ?? $criterias->last();

        $best_id = $request->best_criteria ?? $bestC->id;
        $worst_id = $request->worst_criteria ?? $worstC->id;

        $bo = $request->bo ?? [];
        $ow = $request->ow ?? [];

        // BO untuk best harus 1, OW untuk worst harus 1
        $bo[$best_id] = 1;
        $ow[$worst_id] = 1;

        // Ensure all criteria have values (fallback to 1 if empty)
        foreach ($criterias as $c) {
            if (!isset($bo[$c->id]) || empty($bo[$c->id])) {
                $bo[$c->id] = ($c->code === 'C1') ? 2 : (($c->code === 'C2') ? 3 : (($c->code === 'C3') ? 1 : (($c->code === 'C4') ? 4 : 5)));
            }
            if (!isset($ow[$c->id]) || empty($ow[$c->id])) {
                $ow[$c->id] = ($c->code === 'C1') ? 4 : (($c->code === 'C2') ? 3 : (($c->code === 'C3') ? 5 : (($c->code === 'C4') ? 2 : 1)));
            }
        }

        // Aproksimasi Analitik BWM
        $sum_inv_bo = 0;
        $sum_ow = 0;

        foreach ($criterias as $c) {
            $sum_inv_bo += 1 / max(1, $bo[$c->id]);
            $sum_ow += max(1, $ow[$c->id]);
        }

        $w_bo_list = [];
        $w_ow_list = [];
        $w_avg_list = [];

        foreach ($criterias as $c) {
            $w_bo_list[$c->id] = (1 / max(1, $bo[$c->id])) / $sum_inv_bo;
            $w_ow_list[$c->id] = max(1, $ow[$c->id]) / $sum_ow;
            $w_avg_list[$c->id] = ($w_bo_list[$c->id] + $w_ow_list[$c->id]) / 2;
        }

        // Normalisasi akhir untuk memastikan jumlah = 1
        $total_avg = array_sum($w_avg_list);
        $bwm_results = [];

        foreach ($criterias as $c) {
            $final_weight = $w_avg_list[$c->id] / $total_avg;

            // Simpan ke database
            $c->update(['weight' => $final_weight]);

            $bwm_results[$c->id] = [
                'code' => $c->code,
                'name' => $c->name,
                'bo' => $bo[$c->id],
                'ow' => $ow[$c->id],
                'w_bo' => $w_bo_list[$c->id],
                'w_ow' => $w_ow_list[$c->id],
                'w_avg' => $w_avg_list[$c->id],
                'final_weight' => $final_weight,
                'percentage' => round($final_weight * 100, 2)
            ];
        }

        // Estimasi Konsistensi Xi (Ksi)
        $xi_bo = 0;
        $xi_ow = 0;
        $w_best = $bwm_results[$best_id]['final_weight'];
        $w_worst = $bwm_results[$worst_id]['final_weight'];

        foreach ($criterias as $c) {
            $w_j = $bwm_results[$c->id]['final_weight'];
            $xi_bo = max($xi_bo, abs($w_best - $bo[$c->id] * $w_j));
            $xi_ow = max($xi_ow, abs($w_j - $ow[$c->id] * $w_worst));
        }
        $consistency_ratio = max($xi_bo, $xi_ow);

        // Recalculate TOPSIS automatically
        app(TopsisController::class)->recalculateAll();

        return redirect()->route('bwm.index')->with([
            'success' => 'Perhitungan Bobot BWM berhasil diperbarui dan diterapkan ke perhitungan TOPSIS.',
            'bwm_details' => [
                'best_name' => $criterias->firstWhere('id', $best_id)->name ?? 'Riwayat Kredit',
                'worst_name' => $criterias->firstWhere('id', $worst_id)->name ?? 'Lama Tempat Tinggal',
                'best_code' => $criterias->firstWhere('id', $best_id)->code ?? 'C3',
                'worst_code' => $criterias->firstWhere('id', $worst_id)->code ?? 'C5',
                'sum_inv_bo' => $sum_inv_bo,
                'sum_ow' => $sum_ow,
                'consistency_ratio' => $consistency_ratio,
                'results' => $bwm_results
            ]
        ]);
    }
}
