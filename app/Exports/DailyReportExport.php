<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DailyReportExport implements FromCollection, WithMapping, WithEvents, WithColumnFormatting, WithCustomStartCell, WithDrawings
{
    protected $data, $planData, $summary, $startDate, $endDate, $lineName, $shift, $spv_name, $foreman_name, $leader_name;
    private $currentRow = 0;
    private $actualHeaderStart;

    public function __construct($data, $summary, $startDate, $endDate, $lineName, $shift, $spv_name = null, $foreman_name = null, $leader_name = null)
    {
        $this->data = $data; // Data lengkap untuk Tabel Actual (Merah)
        
        // FILTER: Buat variabel planData khusus Tabel Atas (Kuning) - Bersih dari MAN
        $this->planData = $data->filter(function($item) {
            return !str_contains($item->IdInputHarian, 'MAN');
        });

        $this->summary = $summary;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->lineName = $lineName;
        $this->shift = $shift;
        $this->spv_name = $spv_name;
        $this->foreman_name = $foreman_name;
        $this->leader_name = $leader_name;

        // 🛠️ MODIFIKASI 1: Menaikkan offset jarak dari +3 menjadi +5 agar Tabel Actual (Merah) otomatis start mulai di baris 18
        $this->actualHeaderStart = 11 + count($this->planData) + 5;
    }

    public function startCell(): string { return 'A' . ($this->actualHeaderStart + 3); }
    
    // Tabel bawah tetep nampilin semua data (Actual)
    public function collection() { return $this->data; }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo IPPI');
        $drawing->setPath(public_path('images/logo-ippi.png'));
        $drawing->setHeight(48); 
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(45); 
        $drawing->setOffsetY(12); 
        return $drawing;
    }

    public function map($row): array
    {
        $this->currentRow++;
        $p = $row->plan_data;

        $listRepair = DB::table('prod_detailrepair')->where('IdInputHarian', $row->IdInputHarian)->distinct()->pluck('NamaKerusakan')->implode(', ');
        $listReject = DB::table('prod_detailreject')->where('IdInputHarian', $row->IdInputHarian)->distinct()->pluck('NamaKerusakan')->implode(', ');

        return [
            $this->currentRow, 
            $row->item->JobNumber ?? '-', 
            ($p->PlanQtyA ?? 0) ?: '-', 
            ($p->PlanQtyB ?? 0) ?: '-',
            $row->GoodA ?: '-', 
            $row->GoodB ?: '-', 
            $row->RepairA ?: '-', 
            $row->RepairB ?: '-', 
            (($row->RepairA ?? 0) + ($row->RepairB ?? 0)) ?: '-', // ✅ SEKARANG SUDAH BERSIH BOLO!
            $listRepair ?: '-', 
            $row->RejectA ?: '-', 
            $row->RejectB ?: '-', 
            (($row->RejectA ?? 0) + ($row->RejectB ?? 0)) ?: '-', 
            $listReject ?: '-', 
            $row->AktualQtyA ?: '-', 
            $row->AktualQtyB ?: '-',
            ($p->PlanStarttime ?? '-'), 
            ($p->PlanFinishtime ?? '-'),
            $row->AktualStart ?: '-', 
            $row->AktualFinish ?: '-',
            ($p->TPT ?? 0) ?: '-', 
            $row->TPT ?: '-', 
            ($p->PressTime ?? 0) ?: '-', 
            $row->LineMonitoring ?: '-', 
            $row->LKHCalculation ?: '-', 
            ($p->DiesChangeSoto ?? 0) ?: '-', 
            $row->DiesChange ?: '-', 
            $row->EarlyCheck ?: '-', 
            (($row->DiesChange ?? 0) + ($row->EarlyCheck ?? 0)) ?: '-', 
            $row->AktualWorkTime ?: '-',
            $row->PassRate ? ($row->PassRate / 100) : '-', 
            $row->RepairRate ? ($row->RepairRate / 100) : '-', 
            $row->RejectRate ? ($row->RejectRate / 100) : '-', 
            $row->OEE ? ($row->OEE / 100) : '-', 
            $row->AktualGSPH ?: '-'
        ];
    }

    public function columnFormats(): array {
        return ['C:P' => '#,##0', 'U:AD' => '0.0', 'AE:AH' => '0.00%', 'AI' => '#,##0'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $h = $this->actualHeaderStart;

                foreach(range(1, 5) as $r) { $sheet->getRowDimension($r)->setRowHeight(22); }
                $sheet->getColumnDimension('A')->setWidth(12);
                $sheet->getColumnDimension('B')->setWidth(22);
                $sheet->getColumnDimension('J')->setWidth(20); 
                $sheet->getColumnDimension('N')->setWidth(20); 
                $sheet->getColumnDimension('O')->setWidth(10); 

                // --- 1. HEADER LOGO & INFO ---
                $sheet->mergeCells('A1:B4'); 
                $sheet->mergeCells('A5:B5'); $sheet->setCellValue('A5', "KARAWANG PLANT");
                $sheet->mergeCells('C1:R5'); $sheet->setCellValue('C1', "LAPORAN KERJA HARIAN STAMPING PRESS");
                
                $sheet->getStyle('A5')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('C1')->getAlignment()->setHorizontal('center')->setVertical('center');
                $sheet->getStyle('C1')->getFont()->setBold(true)->setSize(16);
                
                $sheet->setCellValue('A7', 'Line: ' . strtoupper($this->lineName ?: 'Semua Line'));
                $sheet->setCellValue('H7', 'Shift: ' . ($this->shift ?: 'All Shift'));

                if ($this->startDate == $this->endDate) {
                    $tglStr = Carbon::parse($this->startDate)->translatedFormat('d F Y');
                } else {
                    $tglStr = Carbon::parse($this->startDate)->format('d/m/Y') . ' - ' . Carbon::parse($this->endDate)->format('d/m/Y');
                }

                $sheet->setCellValue('O7', 'Periode: ' . $tglStr);
                $sheet->getStyle('A7:O7')->getFont()->setBold(true);

                $ttdCols = [['s'=>'S','e'=>'U','t'=>'Supervisor','n'=>$this->spv_name], ['s'=>'V','e'=>'X','t'=>'Foreman','n'=>$this->foreman_name], ['s'=>'Y','e'=>'AA','t'=>'T.Leader','n'=>$this->leader_name]];
                foreach($ttdCols as $c) {
                    $sheet->mergeCells("{$c['s']}1:{$c['e']}1"); $sheet->setCellValue("{$c['s']}1", $c['t']);
                    $sheet->mergeCells("{$c['s']}2:{$c['e']}4"); 
                    $sheet->mergeCells("{$c['s']}5:{$c['e']}5"); $sheet->setCellValue("{$c['s']}5", $c['n'] ?: '( .......... )');
                    $sheet->getStyle("{$c['s']}1:{$c['e']}5")->getAlignment()->setHorizontal('center')->setVertical('center');
                }
                $sheet->getStyle('A1:AA5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // --- 2. TABEL PLAN (KUNING) ---
                $sheet->getStyle("A8:M10")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
                $sheet->getStyle("A8:M10")->getAlignment()->setHorizontal('center')->setVertical('center');
                $sheet->getStyle("A8:M10")->getFont()->setBold(true);
                $sheet->getStyle("A8:M10")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                $sheet->mergeCells("A8:A10"); $sheet->setCellValue("A8", "No");
                $sheet->mergeCells("B8:B10"); $sheet->setCellValue("B8", "Jobs No");
                $sheet->mergeCells("C8:D8"); $sheet->setCellValue("C8", "Plan QTY");
                $sheet->setCellValue("C9", "A"); $sheet->setCellValue("D9", "B");
                $sheet->mergeCells("E8:F8"); $sheet->setCellValue("E8", "Schedule");
                $sheet->setCellValue("E9", "Start"); $sheet->setCellValue("F9", "Finish");
                $sheet->mergeCells("G8:H10"); $sheet->setCellValue("G8", "Press (Min)");
                $sheet->mergeCells("I8:L8"); $sheet->setCellValue("I8", "Uchi Dandori (Min)");
                $sheet->mergeCells("I9:J9"); $sheet->setCellValue("I9", "Dies Change");
                $sheet->setCellValue("I10", "A"); $sheet->setCellValue("J10", "B");
                $sheet->mergeCells("K9:L9"); $sheet->setCellValue("K9", "1ST Q-Chk"); 
                $sheet->setCellValue("K10", "A"); $sheet->setCellValue("L10", "B");
                $sheet->mergeCells("M8:M10"); $sheet->setCellValue("M8", "Plan GSPH");

                $rowIdx = 11;
                foreach($this->planData as $idx => $d) {
                    $p = $d->plan_data; 
                    $sheet->setCellValue("A$rowIdx", $idx + 1);
                    $sheet->setCellValue("B$rowIdx", $d->item->JobNumber ?? '-');
                    $sheet->setCellValue("C$rowIdx", ($p->PlanQtyA ?? 0) ?: '-');
                    $sheet->setCellValue("D$rowIdx", ($p->PlanQtyB ?? 0) ?: '-');
                    $sheet->setCellValue("E$rowIdx", $p->PlanStarttime ?? '-');
                    $sheet->setCellValue("F$rowIdx", $p->PlanFinishtime ?? '-');
                    $sheet->setCellValue("G$rowIdx", ($p->PressTime ?? 0) ?: '-');
                    $sheet->setCellValue("I$rowIdx", ($p->DiesChangeUchi ?? 0) ?: '-');
                    $sheet->setCellValue("K$rowIdx", ($p->FirstQCheck ?? 0) ?: '-');
                    $sheet->setCellValue("M$rowIdx", ($p->PlanGSPH ?? 0) ?: '-');
                    $rowIdx++;
                }
                $sheet->getStyle("A8:M".($rowIdx-1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("A11:M".($rowIdx-1))->getAlignment()->setHorizontal('center');

                // --- 3. SUMMARY ACHIEVEMENT ---
                $sheet->mergeCells("P8:S8"); $sheet->setCellValue("P8", 'Summary Achievement');
                $sheet->getStyle("P8:S8")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
                $sum = (object)$this->summary;
                $sumData = [['REPAIR', round($sum->RepairRate ?? 0, 2).'%'], ['REJECT', round($sum->RejectRate ?? 0, 2).'%'], ['GSPH', round($sum->AktualGSPH ?? 0)], ['AVAILABILITY', round($sum->Availability ?? 0, 2).'%'], ['PERFORMANCE', round($sum->Performance ?? 0, 2).'%'], ['QUALITY', round($sum->QualityRate ?? 0, 2).'%'], ['OEE', round($sum->OEE ?? 0, 2).'%']];
                foreach($sumData as $idx => $item) {
                    $sRow = 9 + $idx;
                    $sheet->mergeCells("P$sRow:Q$sRow"); $sheet->setCellValue("P$sRow", $item[0]); 
                    $sheet->mergeCells("R$sRow:S$sRow"); $sheet->setCellValue("R$sRow", $item[1]);
                    $sheet->getStyle("P$sRow:S$sRow")->getAlignment()->setHorizontal('center')->setVertical('center');
                }
                $sheet->getStyle("P8:S15")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                
                // 🛠️ MODIFIKASI 2: MEMBERI AREA KOSONG TAMBAHAN DI BAWAH SUMMARY TABLE (BARIS 16 BIAR BERJARAK)
                $sheet->mergeCells("P16:S16");
                $sheet->getStyle("P16:S16")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_NONE);

                // --- 4. HEADER ACTUAL (MERAH) ---
                $sheet->getStyle("A$h:AI".($h+2))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F82B3D');
                $sheet->getStyle("A$h:AI".($h+2))->getFont()->setBold(true)->getColor()->setARGB('FFFFFF');
                $sheet->getStyle("A$h:AI".($h+2))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("A$h:AI".($h+2))->getAlignment()->setHorizontal('center')->setVertical('center');
                
                $sheet->mergeCells("A$h:A".($h+2)); $sheet->setCellValue("A$h", "No");
                $sheet->mergeCells("B$h:B".($h+2)); $sheet->setCellValue("B$h", "Jobs No");
                $sheet->mergeCells("C$h:D".($h+1)); $sheet->setCellValue("C$h", "Plan QTY"); 
                $sheet->mergeCells("E$h:N$h"); $sheet->setCellValue("E$h", "Aktual QTY (PCS)");
                $sheet->mergeCells("O$h:P$h"); $sheet->setCellValue("O$h", "Total Akt");
                $sheet->mergeCells("Q$h:R$h"); $sheet->setCellValue("Q$h", "Plan Sch");
                $sheet->mergeCells("S$h:T$h"); $sheet->setCellValue("S$h", "Aktual Pro");
                $sheet->mergeCells("U$h:V$h"); $sheet->setCellValue("U$h", "TPT");
                $sheet->mergeCells("W$h:W".($h+2)); $sheet->setCellValue("W$h", "Press");
                $sheet->mergeCells("X$h:Y$h"); $sheet->setCellValue("X$h", "CT Akt");
                $sheet->mergeCells("Z$h:Z".($h+2)); $sheet->setCellValue("Z$h", "Soto");
                $sheet->mergeCells("AA$h:AC$h"); $sheet->setCellValue("AA$h", "Uchi (Min)");
                $sheet->mergeCells("AD$h:AD".($h+2)); $sheet->setCellValue("AD$h", "Work");
                $sheet->mergeCells("AE$h:AG$h"); $sheet->setCellValue("AE$h", "Quality");
                $sheet->mergeCells("AH$h:AH".($h+2)); $sheet->setCellValue("AH$h", "OEE");
                $sheet->mergeCells("AI$h:AI".($h+2)); $sheet->setCellValue("AI$h", "GSPH");

                $sheet->mergeCells("E".($h+1).":F".($h+1)); $sheet->setCellValue("E".($h+1), "Good");
                $sheet->mergeCells("G".($h+1).":J".($h+1)); $sheet->setCellValue("G".($h+1), "Repair"); 
                $sheet->mergeCells("K".($h+1).":N".($h+1)); $sheet->setCellValue("K".($h+1), "Reject"); 
                
                $vMerges = ['O'=>'A','P'=>'B','Q'=>'Strt','R'=>'Fnsh','S'=>'Strt','T'=>'Fnsh','U'=>'Pln','V'=>'Act','X'=>'Mnt','Y'=>'LKH','AA'=>'Dies','AB'=>'Chk','AC'=>'Tot','AE'=>'Pas','AF'=>'Rep','AG'=>'Rej'];
                foreach($vMerges as $col => $val) { $sheet->mergeCells($col.($h+1).":".$col.($h+2)); $sheet->setCellValue($col.($h+1), $val); }
                
                $labels = ['C'=>'A','D'=>'B','E'=>'A','F'=>'B','G'=>'A','H'=>'B','I'=>'Tot','J'=>'Nama Kerusakan','K'=>'A','L'=>'B','M'=>'Tot','N'=>'Nama Kerusakan'];
                foreach($labels as $col => $lab) { $sheet->setCellValue($col.($h+2), $lab); }

                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A".($h+3).":AI$lastRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("A".($h+3).":AI$lastRow")->getAlignment()->setHorizontal('center');
                
                $fRow = $lastRow + 2; 
                $sheet->mergeCells("A$fRow:B$fRow"); $sheet->setCellValue("A$fRow", "TOTAL PER SHIFT");
                foreach(['E','F','G','H','I','K','L','M','O','P'] as $col) { $sheet->setCellValue($col.$fRow, "=SUM({$col}".($h+3).":{$col}$lastRow)"); }
                
                $sheet->getStyle("A$fRow:P$fRow")->getFont()->setBold(true);
                $sheet->getStyle("A$fRow:P$fRow")->getAlignment()->setHorizontal('center');
                $sheet->getStyle("A$fRow:P$fRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                $sheet->getPageSetup()->setPrintArea("A1:AI$fRow");
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToWidth(1);
            }
        ];
    }
}