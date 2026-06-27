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
        
        // Retrieve existing assessments
        $assessments = Assessment::where('customer_id', $id)->pluck('value', 'criteria_id')->toArray();

        return view('assessments.form', compact('customer', 'criterias', 'assessments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'values' => 'required|array',
        ]);

        $customerId = $request->customer_id;
        
        // Delete old assessments for this customer
        Assessment::where('customer_id', $customerId)->delete();

        // Insert new ones
        foreach ($request->values as $criteriaId => $value) {
            if ($value !== null) {
                Assessment::create([
                    'customer_id' => $customerId,
                    'criteria_id' => $criteriaId,
                    'value' => $value
                ]);
            }
        }

        return redirect()->route('assessments.index')->with('success', 'Data penilaian berhasil disimpan.');
    }
}
