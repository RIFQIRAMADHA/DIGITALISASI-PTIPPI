<?php

namespace App\Http\Controllers\Produksi\Master\Line;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produksi\Master\ProductionLine;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class LineController extends Controller
{
    public function index()
    {
        $line = ProductionLine::all();
        return view('Produksi.master.productionline.index', compact('line'));
    }

    public function create()
    {
        return view('Produksi.master.productionline.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'NamaProductionLine' => 'required|string|max:255',
            'Shift'              => 'required|string|max:255',
        ], [
            'NamaProductionLine.required' => 'Production Line wajib diisi.',
            'Shift.required'              => 'Shift wajib diisi.',
        ]);

        // Generate ID PLNxxx
        $last = ProductionLine::orderBy('IdProductionLine', 'desc')->first();
        $number = $last ? (int) substr($last->IdProductionLine, 3) + 1 : 1;
        $IdProductionLine = 'PLN' . str_pad($number, 3, '0', STR_PAD_LEFT);

        $line = new ProductionLine();
        $line->IdProductionLine = $IdProductionLine;
        $line->NamaProductionLine = $request->NamaProductionLine;
        $line->Shift = $request->Shift;
        $line->Status = 1; // Otomatis Aktif
        $line->create_by = Auth::user()->NamaKaryawan;
        
        $line->save();

        return redirect()->route('master.productionline.index')
            ->with('success', 'Data Production Line berhasil ditambahkan');
    }

    public function show($id)
    {
        $line = ProductionLine::findOrFail($id);
        return view('Produksi.master.productionline.show', compact('line'));
    }

    public function edit($id)
    {
        $line = ProductionLine::findOrFail($id);
        return view('Produksi.master.productionline.edit', compact('line'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'NamaProductionLine' => 'required|string|max:255',
            'Shift'              => 'required|string|max:255',
            'Status'             => 'required|in:0,1',
        ], [
            'NamaProductionLine.required' => 'Production Line wajib diisi.',
            'Shift.required'              => 'Shift wajib diisi.',
            'Status.required'             => 'Status wajib dipilih.',
        ]);

        $line = ProductionLine::findOrFail($id);
        $line->update([
            'NamaProductionLine' => $request->NamaProductionLine,
            'Shift'              => $request->Shift,
            'Status'             => $request->Status,
            'update_by'          => Auth::user()->NamaKaryawan,
        ]);

        return redirect()->route('master.productionline.index')
            ->with('success', 'Data Production Line berhasil diperbarui');
    }

    public function destroy($id)
    {
        $line = ProductionLine::findOrFail($id);
        // Soft delete/Nonaktifkan
        $line->update(['Status' => 0]);
        return redirect()->route('master.productionline.index')
            ->with('success', 'Production Line berhasil dinonaktifkan');
    }
}