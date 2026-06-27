<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Criteria;
use App\Models\Customer;
use Illuminate\Http\Request;

class TopsisController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('topsis_rank', 'asc')->get();
        return view('topsis.index', compact('customers'));
    }

    public function calculate()
    {
        $criterias = Criteria::all();
        $customers = Customer::all();
        
        // 1. Cek Kelengkapan Data
        if ($criterias->isEmpty() || $customers->isEmpty()) {
            return redirect()->back()->with('error', 'Data kriteria atau nasabah masih kosong.');
        }

        // Cek bobot BWM
        foreach ($criterias as $c) {
            if (is_null($c->weight)) {
                return redirect()->back()->with('error', 'Bobot BWM belum dihitung untuk semua kriteria.');
            }
        }

        // 2. Membentuk Matriks Keputusan X (baris: customer, kolom: criteria)
        $X = [];
        foreach ($customers as $customer) {
            foreach ($criterias as $criteria) {
                $val = Assessment::where('customer_id', $customer->id)
                                 ->where('criteria_id', $criteria->id)
                                 ->value('value') ?? 0;
                $X[$customer->id][$criteria->id] = $val;
            }
        }

        // 3. Normalisasi Matriks (R)
        $pembagi = [];
        foreach ($criterias as $criteria) {
            $sumSq = 0;
            foreach ($customers as $customer) {
                $sumSq += pow($X[$customer->id][$criteria->id], 2);
            }
            $pembagi[$criteria->id] = sqrt($sumSq);
        }

        $R = [];
        foreach ($customers as $customer) {
            foreach ($criterias as $criteria) {
                $val = $X[$customer->id][$criteria->id];
                $div = $pembagi[$criteria->id];
                $R[$customer->id][$criteria->id] = $div == 0 ? 0 : $val / $div;
            }
        }

        // 4. Pembobotan Matriks Normalisasi (V)
        $V = [];
        foreach ($customers as $customer) {
            foreach ($criterias as $criteria) {
                $V[$customer->id][$criteria->id] = $R[$customer->id][$criteria->id] * $criteria->weight;
            }
        }

        // 5. Solusi Ideal Positif (A+) dan Negatif (A-)
        $A_plus = [];
        $A_min = [];
        foreach ($criterias as $criteria) {
            $columnValues = array_column($V, $criteria->id);
            if ($criteria->type == 'benefit') {
                $A_plus[$criteria->id] = max($columnValues);
                $A_min[$criteria->id] = min($columnValues);
            } else { // cost
                $A_plus[$criteria->id] = min($columnValues);
                $A_min[$criteria->id] = max($columnValues);
            }
        }

        // 6. Jarak ke Solusi Ideal (D+ dan D-)
        $D_plus = [];
        $D_min = [];
        foreach ($customers as $customer) {
            $sumDPlus = 0;
            $sumDMin = 0;
            foreach ($criterias as $criteria) {
                $sumDPlus += pow($V[$customer->id][$criteria->id] - $A_plus[$criteria->id], 2);
                $sumDMin += pow($V[$customer->id][$criteria->id] - $A_min[$criteria->id], 2);
            }
            $D_plus[$customer->id] = sqrt($sumDPlus);
            $D_min[$customer->id] = sqrt($sumDMin);
        }

        // 7. Nilai Preferensi (CCi)
        $CC = [];
        foreach ($customers as $customer) {
            $dp = $D_plus[$customer->id];
            $dm = $D_min[$customer->id];
            $cci = ($dp + $dm) == 0 ? 0 : $dm / ($dp + $dm);
            $CC[$customer->id] = $cci;
            
            // Simpan skor sementara
            Customer::where('id', $customer->id)->update(['topsis_score' => $cci]);
        }

        // 8. Perankingan
        arsort($CC); // Urutkan dari nilai terbesar
        $rank = 1;
        foreach ($CC as $custId => $score) {
            Customer::where('id', $custId)->update(['topsis_rank' => $rank]);
            $rank++;
        }

        return redirect()->route('topsis.index')->with('success', 'Perhitungan TOPSIS dan perankingan berhasil dilakukan.');
    }
}
