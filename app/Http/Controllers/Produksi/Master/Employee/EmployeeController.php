<?php

namespace App\Http\Controllers\Produksi\Master\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produksi\Master\Karyawan;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index()
    {
        $karyawan = Karyawan::all();
        return view('Produksi.master.employee.index', compact('karyawan'));
    }

    public function create()
    {
        return view('Produksi.master.employee.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            // Nama tabel diganti menjadi prod_msKaryawan
            'NamaKaryawan' => ['required', 'regex:/^[a-zA-Z\s]+$/', 'unique:prod_msKaryawan,NamaKaryawan'],
            'NRPKaryawan'  => ['required', 'digits_between:1,20', 'unique:prod_msKaryawan,NRPKaryawan'],
            'PasswordKaryawan' => 'required|min:4',
            'Jabatan' => 'required|in:admin,leader,foreman,supervisor,ppc',
        ], [
            'NamaKaryawan.required' => 'Nama Karyawan wajib diisi.',
            'NamaKaryawan.unique'   => 'Nama Karyawan ini sudah terdaftar di sistem.',
            'NamaKaryawan.regex'    => 'Nama hanya boleh berisi huruf dan spasi.',
            'NRPKaryawan.required'  => 'NRP wajib diisi.',
            'NRPKaryawan.unique'    => 'NRP ini sudah digunakan oleh karyawan lain.',
            'NRPKaryawan.digits_between' => 'NRP harus berupa angka (1-20 digit).',
            'PasswordKaryawan.required'  => 'Password wajib diisi.',
            'PasswordKaryawan.min'       => 'Password minimal harus 4 karakter.',
        ]);

        // Generate ID EMPxxx
        $last = Karyawan::orderBy('IdKaryawan', 'desc')->first();
        $number = $last ? ((int) substr($last->IdKaryawan, 3)) + 1 : 1;
        $IdKaryawan = 'EMP' . str_pad($number, 3, '0', STR_PAD_LEFT);

        Karyawan::create([
            'IdKaryawan' => $IdKaryawan,
            'NamaKaryawan' => $request->NamaKaryawan,
            'NRPKaryawan' => $request->NRPKaryawan,
            'PasswordKaryawan' => bcrypt($request->PasswordKaryawan),
            'Jabatan' => $request->Jabatan,
            'Status' => 1,
            'create_by' => Auth::user()->NamaKaryawan,
        ]);

        return redirect()->route('master.employee.index')
            ->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function show(Karyawan $employee)
    {
        $karyawan = $employee;
        return view('Produksi.master.employee.show', compact('karyawan'));
    }

    public function edit(Karyawan $employee)
    {
        $karyawan = $employee;
        return view('Produksi.master.employee.edit', compact('karyawan'));
    }

    public function update(Request $request, Karyawan $employee)
    {
        $request->validate([
            // Nama tabel diganti menjadi prod_msKaryawan dalam Rule::unique
            'NamaKaryawan' => [
                'required', 
                'regex:/^[a-zA-Z\s]+$/', 
                Rule::unique('prod_msKaryawan', 'NamaKaryawan')->ignore($employee->NRPKaryawan, 'NRPKaryawan')
            ],
            'NRPKaryawan' => [
                'required', 
                'digits_between:1,20', 
                Rule::unique('prod_msKaryawan', 'NRPKaryawan')->ignore($employee->NRPKaryawan, 'NRPKaryawan')
            ],
            'Jabatan' => 'required|in:admin,leader,foreman,supervisor,ppc',
            'Status' => 'required|boolean',
        ], [
            'NamaKaryawan.unique' => 'Nama sudah terdaftar di sistem!',
            'NRPKaryawan.unique'  => 'NRP sudah digunakan oleh karyawan lain!',
            'NamaKaryawan.required' => 'Nama Karyawan wajib diisi.',
            'NRPKaryawan.required'  => 'NRP wajib diisi.',
        ]);

        $employee->update([
            'NamaKaryawan' => $request->NamaKaryawan,
            'NRPKaryawan' => $request->NRPKaryawan,
            'PasswordKaryawan' => $request->PasswordKaryawan 
                ? bcrypt($request->PasswordKaryawan) 
                : $employee->PasswordKaryawan,
            'Jabatan' => $request->Jabatan,
            'Status' => $request->Status,
            'update_by' => Auth::user()->NamaKaryawan,
        ]);

        return redirect()->route('master.employee.index')
            ->with('success', 'Data karyawan berhasil diperbarui');
    }

    public function destroy(Karyawan $employee)
    {
        $employee->update(['Status' => 0]);
        return redirect()->route('master.employee.index')
            ->with('success', 'Karyawan berhasil dinonaktifkan');
    }
}