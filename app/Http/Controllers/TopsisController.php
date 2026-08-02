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
        
        if ($criterias->isEmpty() || $customers->isEmpty()) {
            $isReady = false;
        }

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

        $this->recalculateAll();

        // Ambil data nasabah yang sudah di-rank
        $rankedCustomers = Customer::orderBy('topsis_rank', 'asc')->get();

        // Filter untuk role non-admin (opsional)
        $filteredCustomers = [];
        foreach ($rankedCustomers as $cust) {
            $cust->calculated_score = $cust->topsis_score;
            $cust->calculated_rank = $cust->topsis_rank;

            if (in_array(auth()->user()->role, ['mce', 'cap'])) {
                if ($cust->topsis_rank <= 3 || $cust->topsis_score >= 0.5) {
                    $filteredCustomers[] = $cust;
                }
            } else {
                $filteredCustomers[] = $cust;
            }
        }

        return view('topsis.index', [
            'isReady' => true,
            'criterias' => $criterias,
            'customers' => $customers,
            'rankedCustomers' => $filteredCustomers,
        ]);
    }

    public function recalculateAll()
    {
        $criterias = Criteria::all();
        $customers = Customer::all();

        if ($criterias->isEmpty() || $customers->isEmpty()) {
            return false;
        }

        foreach ($criterias as $c) {
            if (is_null($c->weight)) {
                return false;
            }
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
                $A_plus[$criteria->id] = count($columnValues) > 0 ? max($columnValues) : 0;
                $A_min[$criteria->id] = count($columnValues) > 0 ? min($columnValues) : 0;
            } else { // cost
                $A_plus[$criteria->id] = count($columnValues) > 0 ? min($columnValues) : 0;
                $A_min[$criteria->id] = count($columnValues) > 0 ? max($columnValues) : 0;
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

        // 8. Perankingan
        arsort($CC); 
        $rank = 1;
        foreach ($CC as $custId => $score) {
            Customer::where('id', $custId)->update([
                'topsis_score' => $score,
                'topsis_rank' => $rank
            ]);
            $rank++;
        }

        return true;
    }
}
