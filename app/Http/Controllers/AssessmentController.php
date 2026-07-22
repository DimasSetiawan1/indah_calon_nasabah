<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Criteria;
use App\Models\Customer;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('assessments.index', compact('customers'));
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        $criterias = Criteria::all();
        
        // Retrieve existing assessments (using new FK: nasabah_id & kriteria_id)
        $assessments = Assessment::where('nasabah_id', $id)->pluck('value', 'kriteria_id')->toArray();

        return view('assessments.form', compact('customer', 'criterias', 'assessments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:nasabah,id',
            'values' => 'required|array',
        ]);

        $nasabahId = $request->customer_id;
        
        // Hapus penilaian lama untuk nasabah ini
        Assessment::where('nasabah_id', $nasabahId)->delete();

        // Simpan penilaian baru
        foreach ($request->values as $kriteriaId => $value) {
            if ($value !== null) {
                Assessment::create([
                    'nasabah_id'  => $nasabahId,
                    'kriteria_id' => $kriteriaId,
                    'value'       => $value
                ]);
            }
        }

        return redirect()->route('assessments.index')->with('success', 'Data penilaian berhasil disimpan.');
    }
}
