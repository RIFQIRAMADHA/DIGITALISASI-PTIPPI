<?php

namespace App\Http\Controllers\Produksi\Master\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produksi\Master\Customer;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index()
    {
        $customer = Customer::all();
        return view('Produksi.master.customer.index', compact('customer'));
    }

    public function create()
    {
        return view('Produksi.master.customer.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            // Nama tabel diubah menjadi prod_msCustomer
            'NamaCustomer'    => 'required|string|max:255|unique:prod_msCustomer,NamaCustomer',
            'AlamatCustomer'  => 'required|string|max:255',
            'NamaCustomerPIC' => 'required|regex:/^[a-zA-Z\s]+$/',
            'NoTelpCustomer'  => 'required|digits_between:1,13|unique:prod_msCustomer,NoTelpCustomer',
            'EmailCustomer'   => 'required|email|ends_with:@gmail.com|unique:prod_msCustomer,EmailCustomer',
            'NPWPCustomer'    => 'required|digits_between:1,15|unique:prod_msCustomer,NPWPCustomer',
        ], [
            // Pesan Bahasa Indonesia untuk Store tetap sama
            'NamaCustomer.required'    => 'Nama Customer wajib diisi.',
            'NamaCustomer.unique'      => 'Nama Customer ini sudah terdaftar di sistem.',
            'NamaCustomerPIC.regex'    => 'Nama PIC tidak boleh mengandung angka atau simbol.',
            'NoTelpCustomer.unique'    => 'Nomor telepon sudah digunakan oleh customer lain.',
            'NoTelpCustomer.digits_between' => 'Nomor telepon harus angka dan maksimal 13 digit.',
            'EmailCustomer.required'   => 'Email customer wajib diisi.',
            'EmailCustomer.email'      => 'Format alamat email tidak valid.',
            'EmailCustomer.ends_with'  => 'Email harus menggunakan domain @gmail.com.',
            'EmailCustomer.unique'     => 'Email ini sudah digunakan oleh customer lain.',
            'NPWPCustomer.unique'      => 'Nomor NPWP ini sudah terdaftar di sistem.',
            'NPWPCustomer.digits_between' => 'NPWP harus berupa angka dan maksimal 15 digit.',
        ]);

        $last = Customer::orderBy('IdCustomer', 'desc')->first();
        $number = $last ? (int) substr($last->IdCustomer, 3) + 1 : 1;
        $IdCustomer = 'CST' . str_pad($number, 3, '0', STR_PAD_LEFT);

        $customer = new Customer();
        $customer->IdCustomer = $IdCustomer;
        $customer->NamaCustomer = $request->NamaCustomer;
        $customer->AlamatCustomer = $request->AlamatCustomer;
        $customer->NamaCustomerPIC = $request->NamaCustomerPIC;
        $customer->NoTelpCustomer = $request->NoTelpCustomer;
        $customer->EmailCustomer = $request->EmailCustomer;
        $customer->NPWPCustomer = $request->NPWPCustomer;
        $customer->create_by = Auth::user()->NamaKaryawan;
        
        // Otomatis diset 1 (Aktif)
        $customer->Status = 1; 
        
        $customer->save();

        return redirect()->route('master.customer.index')
            ->with('success', 'Data customer berhasil ditambahkan');
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return view('Produksi.master.customer.show', compact('customer'));
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('Produksi.master.customer.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            // Nama tabel diubah menjadi prod_msCustomer dalam Rule::unique
            'NamaCustomer'    => ['required', 'string', 'max:255', Rule::unique('prod_msCustomer', 'NamaCustomer')->ignore($id, 'IdCustomer')],
            'AlamatCustomer'  => 'required|string|max:255',
            'NamaCustomerPIC' => 'required|regex:/^[a-zA-Z\s]+$/',
            'NoTelpCustomer'  => ['required', 'digits_between:1,13', Rule::unique('prod_msCustomer', 'NoTelpCustomer')->ignore($id, 'IdCustomer')],
            'EmailCustomer'   => ['required', 'email', 'ends_with:@gmail.com', Rule::unique('prod_msCustomer', 'EmailCustomer')->ignore($id, 'IdCustomer')],
            'NPWPCustomer'    => ['required', 'digits_between:1,15', Rule::unique('prod_msCustomer', 'NPWPCustomer')->ignore($id, 'IdCustomer')],
            'Status'          => 'required|in:0,1',
        ], [
            'NamaCustomer.unique'   => 'Nama Customer sudah terdaftar di sistem.',
            'NoTelpCustomer.unique' => 'Nomor telepon sudah digunakan oleh customer lain.',
            'EmailCustomer.email'    => 'Format alamat email tidak valid.',
            'EmailCustomer.unique'   => 'Email sudah digunakan oleh customer lain.',
            'NPWPCustomer.unique'    => 'Nomor NPWP ini sudah terdaftar di sistem.',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update([
        'NamaCustomer'    => $request->NamaCustomer,
        'AlamatCustomer'  => $request->AlamatCustomer,
        'NamaCustomerPIC' => $request->NamaCustomerPIC,
        'NoTelpCustomer'  => $request->NoTelpCustomer,
        'EmailCustomer'   => $request->EmailCustomer,
        'NPWPCustomer'    => $request->NPWPCustomer,
        'Status'          => $request->Status,
        'update_by'       => Auth::user()->NamaKaryawan, 
    ]);

        return redirect()->route('master.customer.index')
            ->with('success', 'Data customer berhasil diperbarui');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->update(['Status' => 0]);
        return redirect()->route('master.customer.index')
            ->with('success', 'Customer berhasil dinonaktifkan');
    }
}