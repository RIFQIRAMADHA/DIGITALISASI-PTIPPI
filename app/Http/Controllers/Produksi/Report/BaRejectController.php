<?php

namespace App\Http\Controllers\Produksi\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// --- IMPORT MODEL TRANSAKSI ---
use App\Models\Produksi\Transaksi\TrsInputHarian;

// --- IMPORT MODEL DETAIL BARU ---
use App\Models\Produksi\Detail\DetailReject; //

// --- IMPORT MODEL MASTER BARU ---
use App\Models\Produksi\Master\MsItemProduction; // ✅ FIX
use App\Models\Produksi\Master\MsReject;         // ✅ FIX
use App\Models\Produksi\Master\MsKaryawan;       

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BaRejectExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BaRejectController extends Controller
{
    public function index(Request $request)
    {
        try {
            // 1. Ambil list master material aktif untuk dropdown filter
            $materials = MsItemProduction::where('Status', 1) 
                ->orderBy('JobNumber', 'asc')
                ->get();

            $query = DetailReject::with(['inputHarian.item.customer', 'masterReject', 'item.customer']);

            // 2. DEFINE DATE RANGE: Gunakan awal bulan dan akhir bulan berjalan sebagai default jika inputan kosong
            $startDate = $request->start_date ?? date('Y-m-01');
            $endDate = $request->end_date ?? date('Y-m-t');

            // 3. QUERY INJEKSI FILTER RANGE TANGGAL (Pengecekan lintas tabel inputHarian & created_at)
            $query->where(function($q) use ($startDate, $endDate) {
                $q->whereHas('inputHarian', function($sub) use ($startDate, $endDate) {
                    $sub->whereBetween('TanggalProduksi', [$startDate, $endDate]);
                })->orWhereBetween(\DB::raw('DATE(created_at)'), [$startDate, $endDate]); 
            });

            // 4. FILTER MATERIAL DROPDOWN (Jika Dipilih)
            if ($request->filled('material')) {
                $query->where(function($q) use ($request) {
                    $q->whereHas('inputHarian', function($sub) use ($request) {
                        $sub->where('IdItemProduksi', $request->material);
                    })->orWhere('IdItemProduksi', $request->material);
                });
            }

            // 5. Ambil data dengan sorting paling baru dimasukkan
            $item = $query->orderBy('created_at', 'asc')->paginate(10);

            // Kembalikan variabel start_date dan end_date ke view agar ter-set otomatis di form date
            return view('Produksi.report.bareject.index', compact('item', 'materials', 'startDate', 'endDate'));
            
        } catch (\Exception $e) {
            return "Terjadi Kesalahan: " . $e->getMessage();
        }
    }

    public function create()
    {
        // ✅ FIX: Gunakan MsItemProduction
        $allItems = MsItemProduction::where('Status', 1)
                    ->orderBy('JobNumber', 'asc')
                    ->get();

        $masterReject = MsReject::all();

        return view('Produksi.report.bareject.create', compact('allItems', 'masterReject'));
    }

    public function store(Request $request)
    {
        // 1. VALIDASI DENGAN CUSTOM ERROR MESSAGE SEPERTI DI ITEM
        $request->validate([
            'tanggal_ba'     => 'required|date|before_or_equal:today',
            'IdItemProduksi' => 'required',
            'IdReject'       => 'required',
            'RejectA'        => 'required|numeric|min:1',
            'AreaProblem'    => 'required',
        ], [
            'tanggal_ba.required'        => 'Tanggal BA wajib diisi.',
            'tanggal_ba.date'            => 'Format tanggal tidak valid.',
            'tanggal_ba.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini!',
            
            'IdItemProduksi.required'    => 'Pilih Item Produksi terlebih dahulu.',
            
            'IdReject.required'          => 'Pilih Jenis Reject terlebih dahulu.',
            
            'RejectA.required'           => 'Quantity Reject A wajib diisi.',
            'RejectA.numeric'            => 'Quantity Reject A harus berupa angka.',
            'RejectA.min'                => 'Quantity Reject A minimal harus 1.',
            
            'AreaProblem.required'       => 'Pilih Area Problem terlebih dahulu.',
        ]);

        DB::beginTransaction();
        try {
            $userKaryawan = Auth::user()->NamaKaryawan ?? 'System';
            
            // Ambil tanggal dari input, kalau kosong baru pake now()
            $tanggalInput = $request->tanggal_ba; 

            $idReject = $request->IdReject;
            $idHarian = $request->IdInputHarian ?? 'BA-MANUAL';
            $uniqueId = $idHarian . '-' . $idReject . '-' . bin2hex(random_bytes(2));

            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            DB::table('prod_detailreject')->insert([
                'id'             => $uniqueId,
                'IdInputHarian'  => $idHarian,
                'IdItemProduksi' => $request->IdItemProduksi,
                'IdReject'       => $idReject,
                'Qty'            => $request->RejectA,
                'RejectA'        => $request->RejectA, // Input A
                'RejectB'        => $request->RejectB ?? 0,
                'TipeReject'     => $request->TipeReject ?? 'Dies',
                'NamaKerusakan'  => $request->NamaKerusakan ?? '-',
                'Penyebab'       => $request->Penyebab ?? '-',
                'CounterMeasure' => $request->CounterMeasure ?? '-',
                'NoMasalah'      => '-',
                'AreaProblem'    => $request->AreaProblem,
                'Status'         => 0, 
                'create_by'      => $userKaryawan,
                'update_by'      => $userKaryawan,
                // created_at pake tanggal yang dipilih user
                'created_at'     => $tanggalInput . ' ' . date('H:i:s'), 
                'updated_at'     => now(),
            ]);

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            DB::commit();

            return redirect()->route('report.bareject.index')->with('success', 'Data BA Reject Berhasil Diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            return redirect()->back()->withInput()->with('error', 'Failed to Save: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        // Gunakan first() dan pastikan ID yang dikirim adalah string utuh
        $data = DB::table('prod_detailreject')->where('id', $id)->first();
        
        if (!$data) {
            return redirect()->route('report.bareject.index')->with('error', 'Not Found (ID: '.$id.').');
        }

        $allItems = MsItemProduction::where('Status', 1)->get(); 
        $masterReject = MsReject::all(); 

        return view('Produksi.report.bareject.edit', compact('data', 'allItems', 'masterReject'));
    }

    public function update(Request $request, $id)
    {
        // 1. Validasi
        $request->validate([
            'IdItemProduksi' => 'required',
            'IdReject'       => 'required',
            'RejectA'        => 'required|numeric', 
            'AreaProblem'    => 'required',
        ]);

        // 2. DEBUG: Buka comment DD di bawah ini buat liat beneran gak datanya masuk
        // dd($request->all()); 

        DB::beginTransaction();
        try {
            $userKaryawan = Auth::user()->NamaKaryawan ?? 'System';

            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // 3. GUNAKAN RejectA untuk mengisi Qty
            DB::table('prod_detailreject')->where('id', $id)->update([
                'IdItemProduksi' => $request->IdItemProduksi,
                'IdReject'       => $request->IdReject,
                'Qty'            => (float) $request->RejectA, // <--- INI YG HARUS DIGANTI
                'RejectA'        => (float) $request->RejectA, // <--- PASTIIN INI SAMA
                'RejectB'        => (float) ($request->RejectB ?? 0),
                'TipeReject'     => $request->TipeReject,
                'NamaKerusakan'  => $request->NamaKerusakan,
                'Penyebab'       => $request->Penyebab,
                'CounterMeasure' => $request->CounterMeasure,
                'AreaProblem'    => $request->AreaProblem,
                'update_by'      => $userKaryawan,
                'updated_at'     => now(),
            ]);

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            DB::commit();

            return redirect()->route('report.bareject.index')->with('success', 'Data BA Reject Berhasil Diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            return redirect()->back()->withInput()->with('error', 'Update Failed: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::table('prod_detailreject')->where('id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'Data BA Reject Berhasil Diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        // Gunakan find karena DetailReject sudah kita set primaryKey = 'id' dan incrementing = false
        $data = DetailReject::with([
            'inputHarian.item.customer', 
            'masterReject'
        ])->find($id);

        if (!$data) {
            return redirect()->route('report.bareject.index')->with('error', 'Not Found.');
        }

        return view('Produksi.report.bareject.show', compact('data'));
    }

    public function ambilNomorTerakhir() {
        $sekarang = \Carbon\Carbon::now();
        $catatan = DB::table('prod_nomorba')->where('IdBa', 'BA_REJECT')->first();

        $angkaTerakhir = $catatan ? (int)$catatan->LastNumber : 0;
        $angkaBerikutnya = $angkaTerakhir + 1; 
        
        return response()->json([
            'angka_terakhir' => str_pad($angkaTerakhir, 3, '0', STR_PAD_LEFT), 
            'angka_baru'     => $angkaBerikutnya, 
            'angka_maksimal' => $angkaBerikutnya, // Digunakan JS sebagai gerbang pembanding batas atas <=
            'format_saran'   => "BA / " . str_pad($angkaBerikutnya, 3, '0', STR_PAD_LEFT) . " / PIC - REJECT / " . $sekarang->format('m') . " / " . $sekarang->year
        ]);
    }

    public function getNoBa($id) {
        $data = DB::table('prod_detailreject')->where('id', $id)->first();
        return response()->json(['no_ba' => $data->NoMasalah ?? '-']);
    }

    public function exportExcel(Request $request) {
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-t');
        $selectedIds = $request->ids ? explode(',', $request->ids) : null;
        $noRegister = $request->no_register;

        // 🛠️ FIX KUNCI: Sediakan variabel $tanggal sebagai backup buat view Lu biar gak crash!
        $tanggal = $startDate; 

        $query = DetailReject::query();
        if ($selectedIds) {
            $query->whereIn('id', $selectedIds);
        } else {
            $query->where(function($q) use ($startDate, $endDate) {
                $q->whereHas('inputHarian', function($sub) use ($startDate, $endDate) {
                    $sub->whereBetween('TanggalProduksi', [$startDate, $endDate]);
                })->orWhereBetween(\DB::raw('DATE(created_at)'), [$startDate, $endDate]);
            });
        }

        if ($request->update_counter == 'true') {
            $query->update([
                'Status' => 1,
                'NoMasalah' => $noRegister
            ]); 
        }

        // 🔥 FIX: Cek dulu LastNumber di DB, jangan asal nimpa!
        if ($request->update_counter == 'true' && $request->filled('angka_sekarang')) {
            $inputAngka = (int)$request->angka_sekarang;
            $catatanData = DB::table('prod_nomorba')->where('IdBa', 'BA_REJECT')->first();
            $angkaDatabase = $catatanData ? (int)$catatanData->LastNumber : 0;

            // CUMA update LastNumber kalau angka yang diinput BENERAN LEBIH BESAR
            if ($inputAngka > $angkaDatabase) {
                DB::table('prod_nomorba')->updateOrInsert(
                    ['IdBa' => 'BA_REJECT'], 
                    ['LastNumber' => $inputAngka]
                );
            }
        }

        $item = $query->with(['inputHarian.item.customer', 'masterReject', 'item.customer'])->get();
        if (ob_get_contents()) ob_end_clean();
        
        // Kirim variabel $tanggal ke dalam BaRejectExport
        return Excel::download(new BaRejectExport($item, $tanggal, $noRegister), 'BA-Reject-'.$tanggal.'.xlsx');
    }

    public function exportPdf(Request $request) {
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-t');
        $selectedIds = $request->ids ? explode(',', $request->ids) : null;
        $noRegister = $request->no_register;

        // 🛠️ FIX KUNCI: Sediakan variabel $tanggal sebagai backup buat view PDF Lu biar gak crash!
        $tanggal = $startDate; 

        $queryUpdate = DB::table('prod_detailreject');
        
        if ($selectedIds) {
            $queryUpdate->whereIn('id', $selectedIds);
        } else {
            $queryUpdate->where(function($q) use ($startDate, $endDate) {
                $q->whereExists(function ($sub) use ($startDate, $endDate) {
                    $sub->select(DB::raw(1))
                        ->from('prod_trsinputharian')
                        ->whereColumn('prod_trsinputharian.IdInputHarian', 'prod_detailreject.IdInputHarian')
                        ->whereBetween('prod_trsinputharian.TanggalProduksi', [$startDate, $endDate]);
                })->orWhereBetween(\DB::raw('DATE(prod_detailreject.created_at)'), [$startDate, $endDate]);
            });
        }

        if ($request->update_counter == 'true') {
            $queryUpdate->update([
                'Status' => 1,
                'NoMasalah' => $noRegister
            ]);
        }

        // 🔥 FIX: Terapkan logika penahan mundur yang sama di PDF
        if ($request->update_counter == 'true' && $request->filled('angka_sekarang')) {
            $inputAngka = (int)$request->angka_sekarang;
            $catatanData = DB::table('prod_nomorba')->where('IdBa', 'BA_REJECT')->first();
            $angkaDatabase = $catatanData ? (int)$catatanData->LastNumber : 0;

            // CUMA update LastNumber kalau angka yang diinput BENERAN LEBIH BESAR
            if ($inputAngka > $angkaDatabase) {
                DB::table('prod_nomorba')->updateOrInsert(
                    ['IdBa' => 'BA_REJECT'], 
                    ['LastNumber' => $inputAngka]
                );
            }
        }

        $idsToFetch = DB::table('prod_detailreject')->whereIn('id', $selectedIds ?? $queryUpdate->pluck('id'))->pluck('id');
        $item = DetailReject::with(['inputHarian.item.customer', 'masterReject', 'item.customer'])
                    ->whereIn('id', $idsToFetch)
                    ->get();

        // 🛠️ TAMBAHKAN VARIABEL 'tanggal' KEMBALI KE COMPACT!
        $pdf = Pdf::loadView('Produksi.report.bareject.pdf_view', compact('item', 'tanggal', 'noRegister'))
                    ->setPaper('a4', 'landscape');
                    
        return $pdf->download('BA-Reject-'.$tanggal.'.pdf');
    }
}