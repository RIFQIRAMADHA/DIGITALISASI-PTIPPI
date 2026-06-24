<?php

namespace App\Http\Controllers\Produksi\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Produksi\Master\MsKaryawan;
use App\Models\Produksi\Master\{MsMasalah, MsVerifikasi};

// ✅ PISAHKAN IMPORT TRANSAKSI DAN DETAIL SESUAI FOLDER BARUNYA
use App\Models\Produksi\Transaksi\{TrsQpr, TrsInputHarian};
use App\Models\Produksi\Detail\{DetailMasalah, DetailVerifikasi};

class QprReportController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->get('date'); 

        // Query data QPR
        $query = TrsQpr::with(['inputHarian.item', 'inputHarian.productionLine']);

        if ($tanggal) {
            $query->whereHas('inputHarian', function($q) use ($tanggal) {
                $q->whereDate('TanggalProduksi', $tanggal);
            });
        }

        $dataQPR = $query->orderBy('created_at', 'asc')->paginate(10);

        return view('Produksi.report.qpr.index', [
            'data' => $dataQPR,
            'tanggal' => $tanggal ?? date('Y-m-d')
        ]);
    }

    public function getDetailRow($index)
    {
        $item = TrsInputHarian::with('item')->orderBy('created_at', 'desc')->get();
        return view('Produksi.report.qpr.partials.detail_row', compact('item', 'index'));
    }

    public function create() {
        $item = TrsInputHarian::with('item')->orderBy('created_at', 'desc')->get();
        $masterVerifikasi = MsVerifikasi::all();
        
        // Tarik data karyawan untuk dropdown PIC
        $karyawans = MsKaryawan::where('Status', 1)->orderBy('NamaKaryawan', 'asc')->get();
        
        $existingDetails = collect(); 
        $displayVerifikasi = collect(); 

        return view('Produksi.report.qpr.create', compact('item', 'masterVerifikasi', 'existingDetails', 'displayVerifikasi', 'karyawans'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Bawaan Laravel dengan Pesan Custom (Formal Indonesia)
        $request->validate([
            'IdInputHarian'   => 'required',
            'LokasiKejadian'  => 'required',
            'TanggalProduksi' => 'required|date',
        ], [
            'IdInputHarian.required'   => 'Item / Job Number wajib dipilih.',
            'LokasiKejadian.required'  => 'Lokasi Kejadian wajib diisi.',
            'TanggalProduksi.required' => 'Tanggal Produksi wajib diisi.',
            'TanggalProduksi.date'     => 'Format Tanggal Produksi tidak valid.',
        ]);

        DB::beginTransaction();
        try {
            // 2. Logika Generate Nomor QPR
            $bulan = date('m');
            $tahun = date('Y');
            $lokasi = $request->LokasiKejadian;
            
            // ✅ KODINGAN LEBIH BERSIH KARENA SUDAH DI-IMPORT DI ATAS
            $count = TrsQpr::whereYear('created_at', $tahun)
                            ->whereMonth('created_at', $bulan)
                            ->count();
            
            $urutan = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            $idQpr = "{$urutan}/{$lokasi}/IPPI/{$bulan}/{$tahun}";

            // 3. Simpan Header QPR
            TrsQpr::create([
                'IdQpr'           => $idQpr,
                'NoQpr'           => $idQpr,
                'IdInputHarian'   => $request->IdInputHarian,
                'Rework'          => (float)($request->RepairA ?? 0) + (float)($request->RepairB ?? 0), 
                'Reject'          => (float)($request->RejectA ?? 0) + (float)($request->RejectB ?? 0), 
                'Stok'            => $request->Stok ?? 0,
                'RencanaProduksi' => $request->RencanaProduksi,
                'ProsesRepair'    => $request->ProsesRepair,
                'LokasiKejadian'  => $lokasi,
                'DocReferensi'    => $request->DocReferensi,
                'Jam'             => $request->Jam ?? date('H:i:s'),
                'create_by'       => auth()->user()->NamaKaryawan ?? 'Admin'
            ]);

            // 4. Simpan Detail Masalah
            if ($request->has('masalah')) {
                foreach ($request->masalah as $index => $m) {
                    if (!empty($m['Keterangan']) || !empty($m['AnalisaPenyebab'])) {
                        $statusDampakInt = (isset($m['StatusCorrection2']) && $m['StatusCorrection2'] == 'Closed') ? 1 : 0;

                        // ✅ BERSIH TANPA PATH PANJANG
                        DetailMasalah::create([
                            'IdQpr'            => $idQpr,
                            'IdMasalah'        => $idQpr . '-M' . ($index + 1),
                            'NomorKerusakan'   => $m['NomorKerusakan'] ?? null,
                            'Keterangan'       => $m['Keterangan'] ?? null,
                            'DeskripsiProblem' => $m['DeskripsiProblem'] ?? null,
                            'LastDateProblem'  => $m['LastDateProblem'] ?? null,
                            'AnalisaPenyebab'  => $m['AnalisaPenyebab'] ?? null,
                            'Correction'       => $m['Correction'] ?? null,
                            'TargetCorrection' => $m['TargetCorrection'] ?? null,
                            'PICCorrection'    => $m['PICCorrection'] ?? null,
                            'StatusCorrection' => $m['StatusCorrection'] ?? 'Open',
                            'Correction2'      => $m['Correction2'] ?? null,
                            'TargetCorrection2'=> $m['TargetCorrection2'] ?? null,
                            'PICCorrection2'   => $m['PICCorrection2'] ?? null,
                            'StatusCorrection2'=> $statusDampakInt, 
                        ]);
                    }
                }
            }

            // 5. Simpan Detail Verifikasi
            if ($request->has('verifikasi')) {
                foreach ($request->verifikasi as $index => $v) {
                    if (!empty($v['LangkahPerbaikan'])) {
                        
                        // ✅ BERSIH TANPA PATH PANJANG
                        DetailVerifikasi::create([
                            'IdQpr'             => $idQpr,
                            'IdVerifikasi'      => $idQpr . '-V' . ($index + 1),
                            'LangkahPerbaikan'  => $v['LangkahPerbaikan'],
                            'Schedule'          => $v['Schedule'] ?? null,
                            'TanggalVerifikasi' => $v['TanggalVerifikasi'] ?? null,
                            'MethodeCheck1'     => $v['MethodeCheck1'] ?? null,
                            'MethodeCheck2'     => $v['MethodeCheck2'] ?? null,
                            'MethodeCheck3'     => $v['MethodeCheck3'] ?? null,
                            'Status'            => (isset($v['Status']) && $v['Status'] == 'OK') ? 1 : 0, 
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('report.qpr.index')->with('success', 'Data QPR Berhasil Diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    public function getJobDetail($id)
    {
        // 1. Ambil data Header Input Harian
        $input = TrsInputHarian::with(['item', 'productionLine'])->find($id);

        if ($input) {
            // 2. Tarik data akumulasi Reject dari prod_detailreject
            // Menjumlahkan yang IdInputHarian-nya cocok ATAU BA-MANUAL di tanggal & item yang sama
            $extraReject = \DB::table('prod_detailreject')
                ->where(function($q) use ($id, $input) {
                    $q->where('IdInputHarian', $id)
                    ->orWhere(function($subManual) use ($input) {
                        $subManual->where('IdInputHarian', 'BA-MANUAL')
                                    ->where('IdItemProduksi', $input->IdItemProduksi)
                                    ->whereDate('created_at', $input->TanggalProduksi);
                    });
                })
                ->selectRaw('SUM(RejectA) as totalA, SUM(RejectB) as totalB')
                ->first();

            $shiftValue = $input->Shift ?? ($input->productionLine->Shift ?? '-');

            return response()->json([
                'TanggalProduksi' => date('d-m-Y', strtotime($input->TanggalProduksi)),
                'NamaPart'        => $input->item->NamaPart ?? '-',
                'Model'           => $input->item->Model ?? '-',
                'Shift'           => (string)$shiftValue,
                'Gambar'          => $input->item->Gambar ?? null,
                'RepairA'         => (float)($input->RepairA ?? 0),
                'RepairB'         => (float)($input->RepairB ?? 0),
                // Gabungan Reject Harian + BA Manual
                'RejectA'         => (float)($extraReject->totalA ?? 0),
                'RejectB'         => (float)($extraReject->totalB ?? 0),
            ]);
        }
        return response()->json(['error' => 'Data tidak ditemukan'], 404);
    }

    public function getItemsByDate(Request $request)
    {
        $tanggal = $request->query('tanggal');
        
        if (!$tanggal) {
            return response()->json([]);
        }

        // Tarik data dari prod_trsinputharian berdasarkan tanggal produksi
        // Relasi 'item' dipanggil biar kita bisa akses JobNumber dan NamaPart di JS
        $items = TrsInputHarian::with('item')
            ->whereDate('TanggalProduksi', $tanggal)
            ->get();

        return response()->json($items);
    }

    public function show($id)
    {
        $id = urldecode($id);
        $qpr = TrsQpr::with(['inputHarian.item', 'inputHarian.productionLine', 'detailsVerifikasi', 'detailsMasalah'])
                    ->where('IdQpr', $id)->firstOrFail();

        return view('Produksi.report.qpr.show', compact('qpr'));
    }

    public function edit($id) {
        $id = urldecode($id);
        $qpr = TrsQpr::with(['detailsMasalah', 'detailsVerifikasi', 'inputHarian.item'])
                    ->where('IdQpr', $id)->firstOrFail();

        $item = TrsInputHarian::with('item')->latest()->take(100)->get();
        
        // Tarik data karyawan untuk dropdown PIC
        $karyawans = MsKaryawan::where('Status', 1)->orderBy('NamaKaryawan', 'asc')->get();
        
        $existingDetails = $qpr->detailsMasalah; 
        $displayVerifikasi = $qpr->detailsVerifikasi;

        return view('Produksi.report.qpr.edit', compact('qpr', 'item', 'existingDetails', 'displayVerifikasi', 'karyawans'));
    }

    public function update(Request $request, $id)
    {
        // 1. Force Decode ID because of slashes /
        $decodedId = urldecode($id);

        $request->validate([
            'IdInputHarian' => 'required',
            'LokasiKejadian' => 'required',
        ]);

        \DB::beginTransaction();
        try {
            // 2. Find by IdQpr string
            $qpr = TrsQpr::where('IdQpr', $decodedId)->firstOrFail();
            
            $totalRework = (float)($request->RepairA ?? 0) + (float)($request->RepairB ?? 0);
            $totalReject = (float)($request->RejectA ?? 0) + (float)($request->RejectB ?? 0);

            // 3. Update Header
            $qpr->update([
                'IdInputHarian' => $request->IdInputHarian,
                'Rework'        => $totalRework,
                'Reject'        => $totalReject,
                'Stok'          => $request->Stok ?? 0,
                'RencanaProduksi'=> $request->RencanaProduksi,
                'ProsesRepair'   => $request->ProsesRepair,
                'LokasiKejadian' => $request->LokasiKejadian,
                'DocReferensi'   => $request->DocReferensi,
                'Jam'            => $request->Jam,
                'update_by'      => auth()->user()->NamaKaryawan ?? 'Admin'
            ]);

            // 4. Handle Details (Delete then Insert is cleanest for String PKs)
            $qpr->detailsMasalah()->delete(); 
            if ($request->has('masalah')) {
                foreach ($request->masalah as $index => $m) {
                    if (!empty($m['Keterangan']) || !empty($m['AnalisaPenyebab'])) {
                        $statusDampakInt = (isset($m['StatusCorrection2']) && $m['StatusCorrection2'] == 'Closed') ? 1 : 0;

                        // ✅ BERSIH TANPA RELASI BERSARANG (Konsisten dengan Store)
                        DetailMasalah::create([
                            'IdQpr'            => $qpr->IdQpr, // Tambahkan IdQpr karena tidak lewat relasi
                            'IdMasalah'        => $qpr->IdQpr . '-M' . ($index + 1),
                            'NomorKerusakan'   => $m['NomorKerusakan'] ?? null,
                            'Keterangan'       => $m['Keterangan'] ?? null,
                            'DeskripsiProblem' => $m['DeskripsiProblem'] ?? null,
                            'LastDateProblem'  => $m['LastDateProblem'] ?? null,
                            'AnalisaPenyebab'  => $m['AnalisaPenyebab'] ?? null,
                            'Correction'       => $m['Correction'] ?? null,
                            'TargetCorrection' => $m['TargetCorrection'] ?? null,
                            'PICCorrection'    => $m['PICCorrection'] ?? null,
                            'StatusCorrection' => $m['StatusCorrection'] ?? 'Open',
                            'Correction2'      => $m['Correction2'] ?? null,
                            'TargetCorrection2'=> $m['TargetCorrection2'] ?? null,
                            'PICCorrection2'   => $m['PICCorrection2'] ?? null,
                            'StatusCorrection2'=> $statusDampakInt, 
                        ]);
                    }
                }
            }

            // 5. Handle Verification Details
            $qpr->detailsVerifikasi()->delete();
            if ($request->has('verifikasi')) {
                foreach ($request->verifikasi as $index => $v) {
                    if (!empty($v['LangkahPerbaikan'])) {
                        
                        // ✅ BERSIH TANPA RELASI BERSARANG (Konsisten dengan Store)
                        DetailVerifikasi::create([
                            'IdQpr'             => $qpr->IdQpr, // Tambahkan IdQpr karena tidak lewat relasi
                            'IdVerifikasi'      => $qpr->IdQpr . '-V' . ($index + 1),
                            'LangkahPerbaikan'  => $v['LangkahPerbaikan'],
                            'Schedule'          => $v['Schedule'] ?? null,
                            'TanggalVerifikasi' => $v['TanggalVerifikasi'] ?? null,
                            'MethodeCheck1'     => $v['MethodeCheck1'] ?? null,
                            'Status'            => (isset($v['Status']) && $v['Status'] == 'OK') ? 1 : 0, 
                        ]);
                    }
                }
            }

            \DB::commit();
            return redirect()->route('report.qpr.index')->with('success', 'Data QPR Berhasil Diperbarui');

        } catch (\Exception $e) {
            \DB::rollback();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function addMasalahRow($index) {
        // INI KUNCI AGAR AJAX TIDAK ERROR: Ambil data karyawan di sini
        $karyawans = MsKaryawan::where('Status', 1)->orderBy('NamaKaryawan', 'asc')->get();

        return view('Produksi.report.qpr.partials.masalah_qpr_row', [
            'index' => $index, 
            'item' => null, 
            'karyawans' => $karyawans, // Pastikan dikirim ke view
            'existingDetails' => collect([null])
        ]);
    }

    public function addVerifikasiRow($index) {
        return view('Produksi.report.qpr.partials.verifikasi_qpr_row', [
            'index' => $index, 
            'vItem' => null, 
            'displayVerifikasi' => collect([null]) // Trigger agar muncul 1 baris input baru
        ]);
    }

    public function exportPdf($id)
    {
        $decodedId = urldecode($id);
        $qpr = TrsQpr::with([
                'inputHarian.item', 
                'detailsMasalah', 
                'detailsVerifikasi'
            ])
            ->where('IdQpr', $decodedId)
            ->firstOrFail();

        $pdf = Pdf::loadView('Produksi.report.qpr.pdf', compact('qpr'))
                ->setPaper('a4', 'portrait');

        $safeFileName = str_replace('/', '-', $decodedId);
        return $pdf->stream('QPR_' . $safeFileName . '.pdf');
    }
}