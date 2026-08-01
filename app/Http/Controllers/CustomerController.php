<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Criteria;
use App\Models\Subcriteria;
use App\Models\Assessment;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        $criterias = Criteria::with('subcriterias')->get();
        return view('customers.create', compact('criterias'));
    }

    public function store(Request $request)
    {
        $messages = [
            'nik.required' => 'NIK wajib diisi.',
            'nik.numeric' => 'NIK harus berupa angka.',
            'nik.digits' => 'NIK wajib 16 digit.',
            'nik.unique' => 'NIK ini sudah terdaftar.',
            'name.required' => 'Nama wajib diisi.',
            'name.regex' => 'Nama tidak boleh mengandung angka atau karakter spesial.',
            'address.required' => 'Alamat wajib diisi.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.numeric' => 'Nomor HP harus berupa angka.',
            'criterias.required' => 'Semua pilihan kriteria wajib diisi.',
            'criterias.*.required' => 'Pilihan kriteria wajib diisi.',
        ];

        $request->validate([
            'nik' => 'required|numeric|digits:16|unique:nasabah',
            'name' => 'required|regex:/^[a-zA-Z\s]+$/',
            'address' => 'required',
            'phone' => 'required|numeric',
            'criterias' => 'required|array',
            'criterias.*' => 'required|exists:subkriteria,id',
        ], $messages);

        $customer = Customer::create($request->only(['nik', 'name', 'address', 'phone']));

        if ($request->has('criterias')) {
            foreach ($request->criterias as $criteriaId => $subcriteriaId) {
                $sub = Subcriteria::find($subcriteriaId);
                if ($sub) {
                    Assessment::updateOrCreate(
                        [
                            'nasabah_id' => $customer->id,
                            'kriteria_id' => $criteriaId,
                        ],
                        [
                            'value' => $sub->score,
                        ]
                    );
                }
            }
        }

        return redirect()->route('customers.index')->with('success', 'Data calon nasabah dan pilihan kriteria berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        $criterias = Criteria::with('subcriterias')->get();
        $assessments = Assessment::where('nasabah_id', $customer->id)->get()->keyBy('kriteria_id');

        return view('customers.edit', compact('customer', 'criterias', 'assessments'));
    }

    public function update(Request $request, Customer $customer)
    {
        $messages = [
            'nik.required' => 'NIK wajib diisi.',
            'nik.numeric' => 'NIK harus berupa angka.',
            'nik.digits' => 'NIK wajib 16 digit.',
            'nik.unique' => 'NIK ini sudah terdaftar.',
            'name.required' => 'Nama wajib diisi.',
            'name.regex' => 'Nama tidak boleh mengandung angka atau karakter spesial.',
            'address.required' => 'Alamat wajib diisi.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.numeric' => 'Nomor HP harus berupa angka.',
            'criterias.required' => 'Semua pilihan kriteria wajib diisi.',
            'criterias.*.required' => 'Pilihan kriteria wajib diisi.',
        ];

        $request->validate([
            'nik' => 'required|numeric|digits:16|unique:nasabah,nik,' . $customer->id,
            'name' => 'required|regex:/^[a-zA-Z\s]+$/',
            'address' => 'required',
            'phone' => 'required|numeric',
            'criterias' => 'required|array',
            'criterias.*' => 'required|exists:subkriteria,id',
        ], $messages);

        $customer->update($request->only(['nik', 'name', 'address', 'phone']));

        if ($request->has('criterias')) {
            foreach ($request->criterias as $criteriaId => $subcriteriaId) {
                $sub = Subcriteria::find($subcriteriaId);
                if ($sub) {
                    Assessment::updateOrCreate(
                        [
                            'nasabah_id' => $customer->id,
                            'kriteria_id' => $criteriaId,
                        ],
                        [
                            'value' => $sub->score,
                        ]
                    );
                }
            }
        }

        return redirect()->route('customers.index')->with('success', 'Data calon nasabah dan pilihan kriteria berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Data nasabah berhasil dihapus.');
    }

    public function showDetail(Customer $customer)
    {
        $criterias = Criteria::with('subcriterias')->get();
        $assessments = Assessment::where('nasabah_id', $customer->id)->get()->keyBy('kriteria_id');

        return view('customers.show', compact('customer', 'criterias', 'assessments'));
    }
}
