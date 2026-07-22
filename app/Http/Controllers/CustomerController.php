<?php

namespace App\Http\Controllers;

use App\Models\Customer;
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
        return view('customers.create');
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
        ];

        $request->validate([
            'nik' => 'required|numeric|digits:16|unique:nasabah',
            'name' => 'required|regex:/^[a-zA-Z\s]+$/',
            'address' => 'required',
            'phone' => 'required|numeric',
        ], $messages);

        Customer::create($request->all());
        return redirect()->route('customers.index')->with('success', 'Data nasabah berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
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
        ];

        $request->validate([
            'nik' => 'required|numeric|digits:16|unique:nasabah,nik,' . $customer->id,
            'name' => 'required|regex:/^[a-zA-Z\s]+$/',
            'address' => 'required',
            'phone' => 'required|numeric',
        ], $messages);

        $customer->update($request->all());
        return redirect()->route('customers.index')->with('success', 'Data nasabah berhasil diubah.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Data nasabah berhasil dihapus.');
    }
}
