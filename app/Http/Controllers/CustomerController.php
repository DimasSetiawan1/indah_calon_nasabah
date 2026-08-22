<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Criteria;
use App\Models\Subcriteria;
use App\Models\Assessment;
use App\Http\Controllers\TopsisController;
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
            'nama_barang.required' => 'Nama Barang wajib diisi.',
            'nilai_barang.required' => 'Nilai Barang wajib diisi.',
            'nilai_barang.numeric' => 'Nilai Barang harus berupa angka.',
            'umur.required' => 'Umur wajib diisi.',
            'umur.numeric' => 'Umur harus berupa angka.',
            'kategori_persentase.required' => 'Kategori Nasabah wajib dipilih.',
            'criterias.required' => 'Semua pilihan kriteria wajib diisi.',
            'criterias.*.required' => 'Pilihan kriteria wajib diisi.',
        ];

        $request->validate([
            'nik' => 'required|numeric|digits:16|unique:nasabah',
            'name' => 'required|regex:/^[a-zA-Z\s]+$/',
            'address' => 'required',
            'phone' => 'required|numeric',
            'nama_barang' => 'required|string',
            'nilai_barang' => 'required|numeric',
            'umur' => 'required|numeric',
            'kategori_persentase' => 'required|string',
            'criterias' => 'required|array',
            'criterias.*' => 'required|exists:subkriteria,id',
        ], $messages);

        $kp = explode('|', $request->kategori_persentase);
        $data = $request->only(['nik', 'name', 'address', 'phone', 'nama_barang', 'nilai_barang', 'umur']);
        $data['kategori_nasabah'] = $kp[0] ?? null;
        $data['persentase_pinjaman'] = isset($kp[1]) ? (int) $kp[1] : null;

        $customer = Customer::create($data);

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

        // Otomatis hitung ulang perangkingan TOPSIS dengan bobot default BWM
        app(TopsisController::class)->recalculateAll();

        return redirect()->route('customers.index')->with('success', 'Data calon nasabah dan pilihan kriteria berhasil ditambahkan dan langsung terhitung ke sistem perangkingan.');
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
            'nama_barang.required' => 'Nama Barang wajib diisi.',
            'nilai_barang.required' => 'Nilai Barang wajib diisi.',
            'nilai_barang.numeric' => 'Nilai Barang harus berupa angka.',
            'umur.required' => 'Umur wajib diisi.',
            'umur.numeric' => 'Umur harus berupa angka.',
            'kategori_persentase.required' => 'Kategori Nasabah wajib dipilih.',
            'criterias.required' => 'Semua pilihan kriteria wajib diisi.',
            'criterias.*.required' => 'Pilihan kriteria wajib diisi.',
        ];

        $request->validate([
            'nik' => 'required|numeric|digits:16|unique:nasabah,nik,' . $customer->id,
            'name' => 'required|regex:/^[a-zA-Z\s]+$/',
            'address' => 'required',
            'phone' => 'required|numeric',
            'nama_barang' => 'required|string',
            'nilai_barang' => 'required|numeric',
            'umur' => 'required|numeric',
            'kategori_persentase' => 'required|string',
            'criterias' => 'required|array',
            'criterias.*' => 'required|exists:subkriteria,id',
        ], $messages);

        $kp = explode('|', $request->kategori_persentase);
        $data = $request->only(['nik', 'name', 'address', 'phone', 'nama_barang', 'nilai_barang', 'umur']);
        $data['kategori_nasabah'] = $kp[0] ?? null;
        $data['persentase_pinjaman'] = isset($kp[1]) ? (int) $kp[1] : null;

        $customer->update($data);

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

        // Otomatis hitung ulang perangkingan TOPSIS
        app(TopsisController::class)->recalculateAll();

        return redirect()->route('customers.index')->with('success', 'Data calon nasabah dan pilihan kriteria berhasil diperbarui dan terhitung ulang ke sistem perangkingan.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        app(TopsisController::class)->recalculateAll();

        return redirect()->route('customers.index')->with('success', 'Data nasabah berhasil dihapus.');
    }

    public function showDetail(Customer $customer)
    {
        $criterias = Criteria::with('subcriterias')->get();
        $assessments = Assessment::where('nasabah_id', $customer->id)->get()->keyBy('kriteria_id');

        return view('customers.show', compact('customer', 'criterias', 'assessments'));
    }
}
