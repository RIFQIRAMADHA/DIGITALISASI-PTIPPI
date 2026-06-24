<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithColumnLimit;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class ProductionScheduleImport implements WithMultipleSheets
{
    private $lineType;
    public $authName;

    public function __construct($lineType = 'EF', $authName = 'System')
    {
        $this->lineType = $lineType;
        $this->authName = $authName;
    }

    public function sheets(): array
    {
        if ($this->lineType === 'K') {
            return [
                'Shift Pagi' => new LineKImport("Shift 1", $this),
                'NS'         => new LineKImport("Shift 2", $this),
            ];
        }
        return [
            'SCHEDULE PRESS' => new LineEFImport($this),
        ];
    }
}

class LineEFImport implements ToCollection, WithColumnLimit, WithChunkReading
{
    protected $parent;
    public function __construct($parent) { $this->parent = $parent; }
    public function endColumn(): string { return 'AI'; }
    public function chunkSize(): int { return 100; }

    public function collection(Collection $rows)
    {
        Schema::disableForeignKeyConstraints();
        DB::beginTransaction();
        try {
            $cellD8 = isset($rows[7][3]) ? trim($rows[7][3]) : null;
            $tanggalProduksi = (new ImportHelper)->parseCustomDate($cellD8);
            
            $currentIdPlan = null; 
            $currentLineId = null; 
            $lastGSPH = 0; // Penampung GSPH berantai
            $creator = $this->parent->authName;

            foreach ($rows as $index => $row) {
                $colD = isset($row[3]) ? trim($row[3]) : ''; 
                $colE = isset($row[4]) ? trim($row[4]) : ''; 
                $colAH = isset($row[33]) ? trim($row[33]) : ''; // Kolom AH

                if (str_starts_with($colD, '=')) continue;
                if (str_contains(strtoupper($colE), 'FINISH')) break;

                // Update lastGSPH jika kolom AH baris ini ada isinya
                if (is_numeric($colAH) && $colAH > 0) {
                    $lastGSPH = $colAH;
                }

                if (str_contains(strtoupper($colE), '- LINE')) {
                    $lastGSPH = 0; // Reset GSPH saat ganti header Line
                    $currentShift = null;
                    for ($i = 1; $i <= 5; $i++) {
                        $searchShift = isset($rows[$index + $i][4]) ? strtoupper($rows[$index + $i][4]) : '';
                        if (str_contains($searchShift, 'PAGI')) { $currentShift = "Shift 1"; break; }
                        if (str_contains($searchShift, 'MALAM')) { $currentShift = "Shift 2"; break; }
                    }

                    if ($currentShift) {
                        $currentLineId = (new ImportHelper)->mapLineNameToId($colE, $currentShift);
                        
                        $existing = DB::table('prod_trsplanscheduleproduction')
                                    ->where('IdProductionLine', $currentLineId)
                                    ->where('TanggalProduksi', $tanggalProduksi)
                                    ->first();

                        if ($existing) {
                            (new ImportHelper)->clearOldDataByLine($currentLineId, $tanggalProduksi);
                            $currentIdPlan = $existing->IdPlanSchedule;
                        } else {
                            $currentIdPlan = (new ImportHelper)->generateUniqueIdPlan();
                            DB::table('prod_trsplanscheduleproduction')->insert([
                                'IdPlanSchedule'   => $currentIdPlan,
                                'IdProductionLine' => $currentLineId,
                                'TanggalProduksi'  => $tanggalProduksi,
                                'create_by'        => $creator,
                                'created_at'       => now(),
                            ]);
                        }
                    }
                    continue; 
                }

                if ($currentIdPlan && (new ImportHelper)->isItemRow($colD, 25)) {
                    // Masukkan $lastGSPH ke fungsi insert
                    (new ImportHelper)->executeInsert($row, $currentIdPlan, $currentLineId, $tanggalProduksi, $index, $colD, 'EF', $creator, $lastGSPH);
                }
            }
            DB::commit();
        } catch (\Exception $e) { DB::rollBack(); throw $e; }
    }
}

class LineKImport implements ToCollection, WithColumnLimit, WithChunkReading
{
    private $shift; protected $parent;
    public function __construct($shift, $parent) { $this->shift = $shift; $this->parent = $parent; }
    public function endColumn(): string { return 'AI'; }
    public function chunkSize(): int { return 50; }

    public function collection(Collection $rows)
    {
        Schema::disableForeignKeyConstraints();
        DB::beginTransaction();
        try {
            $cellC17 = isset($rows[16][2]) ? trim($rows[16][2]) : null;
            $tanggalProduksi = (new ImportHelper)->parseCustomDate($cellC17);
            $currentLineId = (new ImportHelper)->mapLineNameToId("LINE K", $this->shift);
            $creator = $this->parent->authName;
            
            $existing = DB::table('prod_trsplanscheduleproduction')
                        ->where('IdProductionLine', $currentLineId)
                        ->where('TanggalProduksi', $tanggalProduksi)
                        ->first();

            if ($existing) {
                (new ImportHelper)->clearOldDataByLine($currentLineId, $tanggalProduksi);
                $currentIdPlan = $existing->IdPlanSchedule;
            } else {
                $currentIdPlan = (new ImportHelper)->generateUniqueIdPlan();
                DB::table('prod_trsplanscheduleproduction')->insert([
                    'IdPlanSchedule'   => $currentIdPlan, 
                    'IdProductionLine' => $currentLineId,
                    'TanggalProduksi'  => $tanggalProduksi, 
                    'create_by'        => $creator, 
                    'created_at'       => now(),
                ]);
            }

            foreach ($rows as $index => $row) {
                $colG = isset($row[6]) ? trim($row[6]) : '';
                if ($currentIdPlan && (new ImportHelper)->isItemRow($colG, 25)) {
                    (new ImportHelper)->executeInsert($row, $currentIdPlan, $currentLineId, $tanggalProduksi, $index, $colG, 'K', $creator, 0);
                }
            }
            DB::commit();
        } catch (\Exception $e) { DB::rollBack(); throw $e; }
    }
}

class ImportHelper {
    
    public function clearOldDataByLine($lineId, $tanggal) {
        $oldSchedules = DB::table('prod_trsplanscheduleproduction')
                        ->where('IdProductionLine', $lineId)
                        ->where('TanggalProduksi', $tanggal)
                        ->get();

        foreach ($oldSchedules as $old) {
            $currentStatus = $old->status;
            $newStatus = empty($currentStatus) ? "Revisi 1" : "Revisi " . ((int)filter_var($currentStatus, FILTER_SANITIZE_NUMBER_INT) + 1);

            DB::table('prod_trsplanscheduleproduction')
                ->where('IdPlanSchedule', $old->IdPlanSchedule)
                ->update(['status' => $newStatus, 'updated_at' => now()]);

            DB::table('prod_detailplanscheduleproduksi')->where('IdPlanSchedule', $old->IdPlanSchedule)->delete();
            DB::table('prod_trsinputharian')->where('IdInputHarian', 'LIKE', 'IH-' . $old->IdPlanSchedule . '-%')->delete();
        }
    }

    public function executeInsert($row, $idPlan, $lineId, $tgl, $index, $searchId, $type, $creator, $passedGSPH = 0) {
        try {
            $item = DB::table('prod_msItemProduction')->where('JobNumber', trim($searchId))->first();
            if (!$item) return;

            $tptValue = ($type === 'EF') ? (is_numeric($row[20]) ? $row[20] : 0) : (is_numeric($row[21]) ? $row[21] : 0);
            $qtyA     = ($type === 'EF') ? (is_numeric($row[5]) ? $row[5] : 0)   : (is_numeric($row[7]) ? $row[7] : 0);
            $ubpValue = ($type === 'EF') ? (is_numeric($row[18]) ? $row[18] : 0) : 0;

            // Logic GSPH: EF pakai AH berantai, K pakai rumus
            $gsphValue = ($type === 'EF') ? $passedGSPH : (($tptValue > 0) ? ($qtyA / $tptValue * 60) : 0);
            $workTimeValue = $tptValue + $ubpValue;

            if ($type === 'EF') {
                DB::table('prod_detailplanscheduleproduksi')->insert([
                    'IdPlanSchedule' => $idPlan,
                    'IdItemProduksi' => $item->IdItemProduksi,
                    'PartName'       => $row[4] ?? $item->NamaPart,
                    'PlanQtyA'       => $qtyA,
                    'PlanQtyB'       => $row[6] ?? 0,
                    'BqSht'          => $row[7] ?? 0,
                    'JmlMaterial'    => $row[8] ?? 0,
                    'JmlPallet'      => $row[10] ?? 0,
                    'CT'             => $row[11] ?? 0,
                    'PressTime'      => $row[12] ?? 0,
                    'FirstQCheck'    => $row[13] ?? 0,
                    'DiesChangeUchi' => $row[14] ?? 0,
                    'DiesChangeSoto' => $row[15] ?? 0,
                    'TotalMesin'     => $row[16] ?? 0,
                    'Stroke'         => $row[17] ?? 0,
                    'UBP'            => $ubpValue,
                    'DTR'            => $row[19] ?? 0,
                    'TPT'            => $tptValue,
                    'PlanStart'      => $this->formatTime($row[21]),
                    'PlanFinish'     => $this->formatTime($row[22]),
                    'QtyMesin1'      => $row[23] ?? 0,
                    'QtyMesin2'      => $row[24] ?? 0,
                    'QtyMesin3'      => $row[25] ?? 0,
                    'QtyMesin4'      => $row[26] ?? 0,
                    'DieChangeHigh'  => $row[27] ?? 0,
                    'PoNumber'       => $row[28] ?? null,
                    'Note'           => $row[30] ?? null,
                    'PlanGSPH'       => $gsphValue,
                    'PlanWorkTime'   => $workTimeValue,
                    'create_by'      => $creator, 
                    'created_at'     => now(),
                ]);
            } else {
                DB::table('prod_detailplanscheduleproduksi')->insert([
                    'IdPlanSchedule' => $idPlan, 
                    'IdItemProduksi' => $item->IdItemProduksi, 
                    'PartName'       => $item->NamaPart,
                    'PoNumber'       => $row[2] ?? null, 
                    'JmlMaterial'    => $row[3] ?? 0, 
                    'JmlPallet'      => $row[5] ?? 0,
                    'PlanQtyA'       => $qtyA, 
                    'PlanQtyB'       => $row[8] ?? 0, 
                    'Stroke'         => $row[9] ?? 0,
                    'TotalMesin'     => $row[13] ?? 0, 
                    'CT'             => $row[14] ?? 0, 
                    'PressTime'      => $row[15] ?? 0,
                    'FirstQCheck'    => $row[16] ?? 0, 
                    'DiesChangeUchi' => $row[17] ?? 0, 
                    'DTR'            => $row[18] ?? 0,
                    'TPT'            => $tptValue, 
                    'PlanStart'      => $this->formatTime($row[23]), 
                    'PlanFinish'     => $this->formatTime($row[24]),
                    'Note'           => $row[27] ?? null, 
                    'QtyMesin1'      => $row[28] ?? 0, 
                    'QtyMesin2'      => $row[29] ?? 0,
                    'QtyMesin3'      => $row[30] ?? 0, 
                    'QtyMesin4'      => $row[31] ?? 0, 
                    'QtyMesin5'      => $row[32] ?? 0,
                    'PlanGSPH'       => $gsphValue, 
                    'PlanWorkTime'   => $workTimeValue,
                    'create_by'      => $creator, 
                    'created_at'     => now(),
                ]);
            }

            DB::table('prod_trsinputharian')->insert([
                'IdInputHarian'    => 'IH-' . $idPlan . '-' . $index . '-' . substr(uniqid(), -3),
                'IdProductionLine' => $lineId,
                'IdItemProduksi'   => $item->IdItemProduksi,
                'TanggalProduksi'  => $tgl,
                'PlanQtyA'         => $qtyA,
                'PlanGSPH'         => $gsphValue,
                'create_by'        => $creator, 
                'created_at'       => now(),
            ]);

        // 🔥 TANGKAP ERROR DI SINI BIAR BISA NGASIH TAU JOB NUMBER-NYA
        } catch (\Illuminate\Database\QueryException $e) {
            // Error ini kepanggil kalau misal tipe data ngaco (Huruf masuk ke kolom angka)
            throw new \Exception(" Cek Job Number [ " . trim($searchId) . " ]. Terdapat input huruf/teks pada kolom yang seharusnya diisi angka!");
        } catch (\Exception $e) {
            // Error umum lainnya
            throw new \Exception("Gagal pada Job Number [ " . trim($searchId) . " ]. Detail: " . $e->getMessage());
        }
    }

    public function isItemRow($val, $maxLen) {
        if (empty($val) || is_numeric($val)) return false;
        $valUpper = strtoupper(trim($val));
        if (str_starts_with($valUpper, '=')) return false;
        $forbidden = ['NO.', 'JOB NUMBER', 'TOTAL', 'TGL', 'JAM', 'REVISI', 'SHIFT', 'OPENING', 'TPM', 'TARGET', 'REMARK'];
        foreach ($forbidden as $word) { if (str_contains($valUpper, $word)) return false; }
        return (strlen($valUpper) >= 2 && strlen($valUpper) <= $maxLen);
    }

    public function formatTime($val) {
        if (empty($val) || $val == '0') return '00:00';
        try {
            if (is_numeric($val)) return Date::excelToDateTimeObject($val)->format('H:i');
            $cleanStr = trim($val);
            if (strpos($cleanStr, ':') !== false) return Carbon::parse($cleanStr)->format('H:i');
            return '00:00';
        } catch (\Exception $e) { return '00:00'; }
    }

    public function parseCustomDate($val) {
        if (empty($val)) return null;
        if (is_numeric($val)) return Date::excelToDateTimeObject($val)->format('Y-m-d');
        try { return Carbon::parse($val)->format('Y-m-d'); } catch (\Exception $e) { return null; }
    }

    public function mapLineNameToId($text, $shift) {
        $text = strtoupper($text);
        $nama = str_contains($text, 'LINE K') ? "Line K" : "Line " . trim(str_replace('- LINE', '', $text));
        $res = DB::table('prod_msproductionline')->where('NamaProductionLine', $nama)->where('Shift', $shift)->first();
        if (!$res) throw new \Exception("Line '{$nama}' Shift '{$shift}' tidak ditemukan.");
        return $res->IdProductionLine;
    }

    public function generateUniqueIdPlan() {
        $last = DB::table('prod_trsplanscheduleproduction')->orderBy('IdPlanSchedule', 'desc')->first();
        $number = $last ? (int) substr($last->IdPlanSchedule, 2) + 1 : 1;
        return 'PS' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}