<?php

namespace App\Http\Controllers\Produksi\Master\Item;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produksi\Master\ItemProduction;
use App\Models\Produksi\Master\Customer;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index()
    {
        $item = ItemProduction::with('customer')->get();
        return view('Produksi.master.itemproduction.index', compact('item'));
    }

    public function create()
    {
        $customers = Customer::where('Status', 1)->get();
        return view('Produksi.master.itemproduction.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'IdCustomer' => 'required',
            // Nama tabel diubah menjadi prod_msItemProduction
            'JobNumber'  => 'required|unique:prod_msItemProduction,JobNumber',
            'PartNumber' => 'required|unique:prod_msItemProduction,PartNumber',
            'NamaPart'   => 'required|unique:prod_msItemProduction,NamaPart',
            'Model'      => 'required',
            'Gambar'     => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'JobNumber.unique'    => 'Job Number ini sudah terdaftar di sistem.',
            'PartNumber.unique'   => 'Part Number ini sudah terdaftar di sistem.',
            'NamaPart.unique'     => 'Nama Part ini sudah terdaftar di sistem.',
            'Gambar.required'     => 'Gambar produk wajib diunggah!',
            'Gambar.image'        => 'Berkas harus berupa gambar.',
            'IdCustomer.required' => 'Pilih Customer terlebih dahulu.',
        ]);

        // Generate ID
        $last = ItemProduction::orderBy('IdItemProduksi', 'desc')->first();
        $number = $last ? (int) substr($last->IdItemProduksi, 3) + 1 : 1;
        $IdItemProduksi = 'ITM' . str_pad($number, 4, '0', STR_PAD_LEFT);

        $gambarPath = null;
        if ($request->hasFile('Gambar')) {
            $gambarPath = $request->file('Gambar')->store('itemproduction', 'public');
        }

        // SIMPAN DATA
        $item = new ItemProduction();
        $item->IdItemProduksi = $IdItemProduksi;
        $item->IdCustomer     = $request->IdCustomer;
        $item->JobNumber      = $request->JobNumber;
        $item->PartNumber     = $request->PartNumber;
        $item->NamaPart       = $request->NamaPart;
        $item->Model          = $request->Model;
        $item->Gambar         = $gambarPath;
        $item->create_by = Auth::user()->NamaKaryawan;
        
        // Set Otomatis Aktif (1) tanpa mengambil dari request
        $item->Status         = 1; 
        
        $item->save();

        return redirect()
            ->route('master.itemproduction.index')
            ->with('success', 'Item Production berhasil ditambahkan');
    }

    public function edit($id)
    {
        // Mengambil data item yang akan diedit
        $item = ItemProduction::findOrFail($id);

        // Mengambil data customer aktif untuk pilihan dropdown di form edit
        $customers = Customer::where('Status', 1)->get();

        return view('Produksi.master.itemproduction.edit', compact('item', 'customers'));
    }

    public function update(Request $request, $id)
    {
        $item = ItemProduction::findOrFail($id);

        $request->validate([
            'IdCustomer' => 'required',
            // Nama tabel diubah menjadi prod_msItemProduction dalam Rule::unique
            'JobNumber'  => ['required', Rule::unique('prod_msItemProduction', 'JobNumber')->ignore($id, 'IdItemProduksi')],
            'PartNumber' => ['required', Rule::unique('prod_msItemProduction', 'PartNumber')->ignore($id, 'IdItemProduksi')],
            'NamaPart'   => ['required', Rule::unique('prod_msItemProduction', 'NamaPart')->ignore($id, 'IdItemProduksi')],
            'Model'      => 'required',
            'Gambar'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'Status'     => 'required|in:0,1',
        ], [
            'JobNumber.unique'  => 'Job Number sudah digunakan oleh item lain.',
            'PartNumber.unique' => 'Part Number sudah digunakan oleh item lain.',
            'NamaPart.unique'   => 'Nama Part sudah digunakan oleh item lain.',
        ]);

        $gambarPath = $item->Gambar;
        if ($request->hasFile('Gambar')) {
            $gambarPath = $request->file('Gambar')->store('itemproduction', 'public');
        }

        $item->update([
            'IdCustomer' => $request->IdCustomer,
            'JobNumber'  => $request->JobNumber,
            'PartNumber' => $request->PartNumber,
            'NamaPart'   => $request->NamaPart,
            'Model'      => $request->Model,
            'Gambar'     => $gambarPath,
            'Status'     => $request->Status,
            'update_by'       => Auth::user()->NamaKaryawan, 
        ]);

        return redirect()
            ->route('master.itemproduction.index')
            ->with('success', 'Data Item Production berhasil diperbarui');
    }

    public function show($id)
    {
        // Mengambil data item beserta data customernya
        $item = ItemProduction::with('customer')->findOrFail($id);
        
        return view('Produksi.master.itemproduction.show', compact('item'));
    }

    public function destroy($id)
    {
        $item = ItemProduction::findOrFail($id);
        $item->update(['Status' => 0]);

        return redirect()
            ->route('master.itemproduction.index')
            ->with('success', 'Item Production berhasil dinonaktifkan');
    }
}