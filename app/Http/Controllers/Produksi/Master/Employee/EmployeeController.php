<?php

namespace App\Http\Controllers\Produksi\Master\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produksi\Master\Karyawan;

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
            'NamaKaryawan' => 'required',
        ]);

        // Ambil ID terakhir
        $last = Karyawan::orderBy('idKaryawan', 'desc')->first();

        $number = 1;
        if ($last) {
            $number = (int) substr($last->idKaryawan, 3) + 1;
        }

        $idKaryawan = 'EMP' . str_pad($number, 3, '0', STR_PAD_LEFT);

        Karyawan::create([
            'idKaryawan'   => $idKaryawan,
            'NamaKaryawan' => $request->NamaKaryawan,
            'NRPKaryawan'  => $request->NRPKaryawan,
            'Jabatan'      => $request->Jabatan,
            'Status'       => $request->Status ?? 1,
        ]);

        return redirect()
            ->route('master.employee.index')
            ->with('success', 'Data berhasil ditambah');
    }


    public function show($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        return view('Produksi.master.employee.show', compact('karyawan'));
    }

    public function edit($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        return view('Produksi.master.employee.edit', compact('karyawan'));
    }

    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $karyawan->update([
            'NamaKaryawan' => $request->NamaKaryawan,
            'NRPKaryawan'  => $request->NRPKaryawan,
            'Jabatan'      => $request->Jabatan,
            'Status'       => $request->Status,
        ]);

        return redirect()
            ->route('master.employee.index')
            ->with('success', 'Data karyawan berhasil diubah');
    }

    /**
     * DELETE = NONAKTIFKAN
     */
    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->update([
            'Status' => 0
        ]);

        return redirect()
            ->route('master.employee.index')
            ->with('success', 'Karyawan berhasil dinonaktifkan');
    }
}
