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
        $criterias = Criteria::all();
        $customers = Customer::all();
        
        $isReady = true;
        
        // 1. Cek Kelengkapan Data
        if ($criterias->isEmpty() || $customers->isEmpty()) {
            $isReady = false;
        }

        // Cek bobot BWM
        foreach ($criterias as $c) {
            if (is_null($c->weight)) {
                $isReady = false;
                break;
            }
        }

        if (!$isReady) {
            return view('topsis.index', [
                'isReady' => false,
                'errorMsg' => 'Data kriteria/nasabah kosong atau bobot BWM belum dihitung.'
            ]);
        }

        // 2. Membentuk Matriks Keputusan X
        $X = [];
        foreach ($customers as $customer) {
            foreach ($criterias as $criteria) {
                $val = Assessment::where('nasabah_id', $customer->id)
                                 ->where('kriteria_id', $criteria->id)
                                 ->value('value') ?? 0;
                $X[$customer->id][$criteria->id] = (float)$val;
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
        }

        // 8. Perankingan (hanya untuk array view)
        arsort($CC); 
        $rank = 1;
        $rankedCustomers = [];
        foreach ($CC as $custId => $score) {
            // Update db for persistence (optional but good for tracking)
            Customer::where('id', $custId)->update([
                'topsis_score' => $score,
                'topsis_rank' => $rank
            ]);
            
            $cust = $customers->firstWhere('id', $custId);
            $cust->calculated_score = $score;
            $cust->calculated_rank = $rank;

            // Untuk MCE dan CAP, hanya tampilkan yang LAYAK (rank <= 3 atau score >= 0.5)
            if (in_array(auth()->user()->role, ['mce', 'cap'])) {
                if ($rank <= 3 || $score >= 0.5) {
                    $rankedCustomers[] = $cust;
                }
            } else {
                $rankedCustomers[] = $cust;
            }
            
            $rank++;
        }

        return view('topsis.index', compact(
            'isReady', 'criterias', 'customers', 'rankedCustomers',
            'X', 'R', 'V', 'A_plus', 'A_min', 'D_plus', 'D_min', 'CC'
        ));
    }
}
