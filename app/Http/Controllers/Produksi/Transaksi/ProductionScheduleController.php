<?php

namespace App\Http\Controllers\Produksi\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Produksi\Master\ProductionLine;
use App\Models\Produksi\Master\Karyawan;
use App\Models\Produksi\Master\ItemProduction;
use App\Models\Produksi\Transaksi\TrsPlanScheduleProduction;
use App\Models\Produksi\Transaksi\DetailPlanScheduleProduksi;

class ProductionScheduleController extends Controller
{
    public function index(Request $request)
    {
        $lines = ProductionLine::where('Status', 1)->get();
        $tanggal = $request->get('date', date('Y-m-d'));
        $lineId = $request->get('line');

        $selectedLine = null;
        if ($lineId) {
            $selectedLine = ProductionLine::where('IdProductionLine', $lineId)->first();
        }

        $query = TrsPlanScheduleProduction::with('productionLine')
                    ->whereDate('TanggalProduksi', $tanggal);

        if ($lineId) {
            $query->where('IdProductionLine', $lineId);
        }

        $schedules = $query->get();

        return view('Produksi.productionschedule.index', compact('lines', 'schedules', 'tanggal', 'selectedLine'));
    }

    public function create()
    {
        $lines = ProductionLine::where('Status', 1)->get();
        $karyawan = Karyawan::where('Status', 1)->get();
        $item = ItemProduction::where('Status', 1)->get();
        return view('Produksi.productionschedule.create', compact('lines', 'karyawan', 'item'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'IdProductionLine' => 'required',
            // Validasi: tidak boleh kurang dari hari ini
            'TanggalProduksi' => 'required|date|after_or_equal:today',
            'details' => 'required|array|min:1',
            'details.*.IdItemProduksi' => 'required',
        ], [
            // Pesan kustom agar user tidak bingung
            'TanggalProduksi.after_or_equal' => 'Tanggal produksi tidak boleh kurang dari hari ini.',
        ]);

        $items = collect($request->details)->pluck('IdItemProduksi');
        if ($items->duplicates()->isNotEmpty()) {
            return back()->withErrors(['error' => 'Terdapat Item Produksi yang sama dalam satu jadwal.'])->withInput();
        }
        
        DB::beginTransaction();
        try {
            // --- LOGIKA ID SEPERTI CUSTOMER ---
            $last = TrsPlanScheduleProduction::orderBy('IdPlanSchedule', 'desc')->first();
            $number = $last ? (int) substr($last->IdPlanSchedule, 2) + 1 : 1;
            $IdPlanSchedule = 'PS' . str_pad($number, 3, '0', STR_PAD_LEFT); 

            $header = new TrsPlanScheduleProduction();
            $header->IdPlanSchedule = $IdPlanSchedule;
            $header->IdProductionLine = $request->IdProductionLine;
            $header->NamaPIC = $request->NamaPIC;
            $header->TanggalProduksi = $request->TanggalProduksi;
            $header->create_by = Auth::user()->NamaKaryawan ?? 'System';
            $header->save();

            foreach ($request->details as $detail) {
                DetailPlanScheduleProduksi::create([
                    'IdPlanSchedule' => $header->IdPlanSchedule,
                    'IdItemProduksi' => $detail['IdItemProduksi'],
                    'PlanQtyA'       => $detail['PlanQty1'] ?? 0,
                    'PlanQtyB'       => $detail['PlanQty2'] ?? 0,
                    'PlanStart'      => $detail['StartProduksi'],
                    'PlanFinish'     => $detail['FinishProduksi'],
                    'PressTime'      => $detail['PressTime'] ?? 0,
                    'DiesChangeUchi' => $detail['Uchi'] ?? 0,
                    'DiesChangeSoto' => $detail['Soto'] ?? 0,
                    'FirstQCheckA'   => $detail['FirstQCheckA'] ?? 0,
                    'FirstQCheckB'   => $detail['FirstQCheckB'] ?? 0,
                    'TPT'            => $detail['TPT'] ?? 0,
                    'UBP'            => $detail['UBP'] ?? 0,
                    'DTR'            => $detail['DTR'] ?? 0,
                    'PlanWorkTime'   => $detail['WorkTime'] ?? 0,
                    'PlanGSPH'       => $detail['GSPH'] ?? 0,
                    'Stroke'         => $detail['Stroke'] ?? 0,
                    'Note'           => $detail['Note'] ?? null,
                    'create_by'      => Auth::user()->NamaKaryawan ?? 'System',
                ]);
            }

            DB::commit();
            return redirect()->route('productionschedule.index')->with('success', 'Jadwal Berhasil Disimpan!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Gagal Simpan: ' . $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $schedule = TrsPlanScheduleProduction::with(['productionLine', 'details.item'])->findOrFail($id);
        return view('Produksi.productionschedule.show', compact('schedule'));
    }

    public function edit($id)
    {
        $schedule = TrsPlanScheduleProduction::with('details')->findOrFail($id);
        $lines = ProductionLine::where('Status', 1)->get();
        $karyawan = Karyawan::where('Status', 1)->get();
        $item = ItemProduction::where('Status', 1)->get();
        return view('Produksi.productionschedule.edit', compact('schedule', 'lines', 'karyawan', 'item'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'IdProductionLine' => 'required',
            'TanggalProduksi' => 'required|date',
            'details' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $header = TrsPlanScheduleProduction::findOrFail($id);
            $header->IdProductionLine = $request->IdProductionLine;
            $header->NamaPIC = $request->NamaPIC;
            $header->TanggalProduksi = $request->TanggalProduksi;
            $header->update_by = Auth::user()->NamaKaryawan ?? 'System';
            $header->save();

            DetailPlanScheduleProduksi::where('IdPlanSchedule', $id)->delete();
            
            foreach ($request->details as $detail) {
                DetailPlanScheduleProduksi::create([
                    'IdPlanSchedule' => $id,
                    'IdItemProduksi' => $detail['IdItemProduksi'],
                    'PlanQtyA'       => $detail['PlanQty1'] ?? 0,
                    'PlanQtyB'       => $detail['PlanQty2'] ?? 0,
                    'PlanStart'      => $detail['StartProduksi'],
                    'PlanFinish'     => $detail['FinishProduksi'],
                    'PressTime'      => $detail['PressTime'] ?? 0,
                    'DiesChangeUchi' => $detail['Uchi'] ?? 0,
                    'DiesChangeSoto' => $detail['Soto'] ?? 0,
                    'FirstQCheckA'   => $detail['FirstQCheckA'] ?? 0,
                    'FirstQCheckB'   => $detail['FirstQCheckB'] ?? 0,
                    'TPT'            => $detail['TPT'] ?? 0,
                    'UBP'            => $detail['UBP'] ?? 0,
                    'DTR'            => $detail['DTR'] ?? 0,
                    'PlanWorkTime'   => $detail['WorkTime'] ?? 0,
                    'PlanGSPH'       => $detail['GSPH'] ?? 0,
                    'Stroke'         => $detail['Stroke'] ?? 0,
                    'Note'           => $detail['Note'] ?? null,
                    'create_by'      => $header->create_by,
                    'update_by'      => Auth::user()->NamaKaryawan ?? 'System',
                ]);
            }

            DB::commit();
            return redirect()->route('productionschedule.index')->with('success', 'Jadwal berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DetailPlanScheduleProduksi::where('IdPlanSchedule', $id)->delete();
            TrsPlanScheduleProduction::destroy($id);
            return redirect()->route('productionschedule.index')->with('success', 'Data berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data.');
        }
    }

    public function getDetailRow($index)
    {
        $item = ItemProduction::where('Status', 1)->get();
        return view('Produksi.productionschedule.partials.detail_row', compact('item', 'index'))->render();
    }
}