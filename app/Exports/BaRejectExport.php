<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Carbon\Carbon;

class BaRejectExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithColumnFormatting, WithCustomStartCell
{
    protected $item;
    protected $tanggal;
    protected $noRegister;
    private $currentRow = 0;
    private $dataStartRow = 12;

    public function __construct($item, $tanggal, $noRegister = null)
    {
        $this->item = $item;
        $this->tanggal = $tanggal;
        $this->noRegister = $noRegister;
    }

    public function startCell(): string
    {
        return 'A' . $this->dataStartRow;
    }

    public function collection()
    {
        return $this->item;
    }

    public function headings(): array { return []; }

    public function map($row): array
    {
        $this->currentRow++;
        
        $harian = optional($row->inputHarian);
        $prodItem = ($harian->exists && $harian->item) ? $harian->item : $row->item;
        
        $qty = (float)($row->Qty ?? 0);
        $beratPcs = (float)(optional($prodItem)->Berat ?? 0);
        
        $areaManual = $row->TipeReject; 
        $areaMaster = optional($row->masterReject)->TipeReject;
        $area = strtolower($areaManual ?? $areaMaster ?? '');
        
        $namaCust = strtoupper(optional(optional($prodItem)->customer)->NamaCustomer ?? '');
        $isADM = str_contains($namaCust, 'ADM');

        $tanggalBaris = $harian->exists 
            ? Carbon::parse($harian->TanggalProduksi)->format('d-M-y') 
            : Carbon::parse($row->created_at)->format('d-M-y');

        return [
            $this->currentRow,
            $tanggalBaris,
            optional($prodItem)->JobNumber ?? '-',
            $qty,
            $beratPcs,
            $qty * $beratPcs,
            (in_array($area, ['dies', 'op-10']) ? $qty : null),
            (in_array($area, ['machine', 'mach', 'op-20']) ? $qty : null),
            (in_array($area, ['material', 'mat', 'op-30']) ? $qty : null),
            (in_array($area, ['method', 'meth', 'op-40']) ? $qty : null),
            $row->NamaKerusakan ?? (optional($row->masterReject)->NamaReject ?? '-'),
            $row->Penyebab ?? '-',
            $row->CounterMeasure ?? '-',
            (!$isADM ? 'IPPI' : '-'),
            ($isADM ? 'ADM' : '-')
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => '#,##0',
            'E' => '#,##0.00',
            'F' => '#,##0.00',
            'G' => '#,##0',
            'H' => '#,##0',
            'I' => '#,##0',
            'J' => '#,##0',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                
                if ($lastRow < $this->dataStartRow) {
                    $lastRow = $this->dataStartRow;
                }
                
                $totalRow = $lastRow + 1;
                $costRow = $totalRow + 2;

                // 1. SET WIDTH
                $widths = [
                    'A'=>5,'B'=>12,'C'=>15,'D'=>8,'E'=>10,'F'=>12,
                    'G'=>8,'H'=>8,'I'=>8,'J'=>8,'K'=>18,'L'=>25,'M'=>25,
                    'N'=>10,'O'=>10
                ];
                foreach ($widths as $col => $w) { $sheet->getColumnDimension($col)->setWidth($w); }

                // 2. HEADER ATAS
                $sheet->mergeCells('A1:O1');
                $sheet->setCellValue('A1', 'SCRAP EX PRODUKSI');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(20)->setUnderline(true);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A2', 'TANGGAL PENGELUARAN: ' . Carbon::parse($this->tanggal)->format('d/m/Y'));
                
                $sheet->mergeCells('G2:I2');
                $sheet->setCellValue('G2', 'NOMOR REGISTER');
                $sheet->getStyle('G2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                $sheet->mergeCells('J2:L2');
                $sheet->setCellValue('J2', $this->noRegister ?? 'BA / .... / PIC - REJECT / ....');
                
                $sheet->mergeCells('M2:O2');
                $sheet->setCellValue('M2', 'Line : STAMPING E / F / K - LINE');
                $sheet->getStyle('M2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // 3. AREA TANDA TANGAN
                $sheet->mergeCells('A4:C4'); $sheet->setCellValue('A4', 'Dibuat');
                $sheet->mergeCells('D4:H4'); $sheet->setCellValue('D4', 'Disetujui');
                $sheet->setCellValue('I4', 'Diterima');
                $sheet->getStyle('A4:I4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('EEEEEE');

                $names = ["Foreman Prod.", "Foreman QC", "Foreman D.S", "( M. Azka )", "( Ruri S. )", "( Ilham M.W )", "( Eko H )", "( Sriyanto )", "( Adang K )"];
                $titles = ["", "", "", "Ka. Sie Prod", "Ka. Sie MAD", "Ka. Sie MTC & QA", "Ka. Dept.", "Division Head", "G.A."];
                
                foreach(range('A','I') as $i => $col) { 
                    $sheet->setCellValue($col.'5', $names[$i]); 
                    $sheet->setCellValue($col.'7', $titles[$i]); 
                }

                $sheet->getRowDimension(6)->setRowHeight(50); 
                $sheet->getStyle('A4:I7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A4:I7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                
                $sheet->mergeCells('K4:O7');
                $sheet->setCellValue('K4', "Dies: Dies Problem\nMach: Machine Problem\nMat: Material Problem\nMeth: Methode Handling & Setting Problem");
                $sheet->getStyle('K4')->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('K4:O7')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

                // 4. HEADER TABEL
                $sheet->setCellValue('A9', 'LIST PENGELUARAN SCRAP EX PRODUKSI');
                $sheet->mergeCells('A9:O9');
                $sheet->getStyle('A9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A9')->getFont()->setBold(true);

                $headers = [
                    'A10'=>'NO','B10'=>'TANGGAL','C10'=>'JOB NUMBER','D10'=>'QTY','E10'=>'BERAT/PCS','F10'=>'BERAT TOTAL',
                    'G10'=>'PENYEBAB SCRAP','G11'=>'DIES','H11'=>'MACH','I11'=>'MAT','J11'=>'METH',
                    'K10'=>'JENIS KERUSAKAN','L10'=>'PENYEBAB','M10'=>'COUNTER MEASURE',
                    'N10'=>'MATERIAL','N11'=>'IPPI','O11'=>'CUSTOMER'
                ];
                foreach($headers as $cell => $val) { $sheet->setCellValue($cell, $val); }
                
                foreach(['A','B','C','D','E','F','K','L','M'] as $col) { $sheet->mergeCells($col.'10:'.$col.'11'); }
                $sheet->mergeCells('G10:J10'); 
                $sheet->mergeCells('N10:O10');

                $headerStyle = $sheet->getStyle('A10:O11');
                $headerStyle->getFont()->setBold(true)->getColor()->setARGB('FFFFFF');
                $headerStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F82B3D');
                $headerStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $headerStyle->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // 🛠️ PERBAIKAN UTAMA: Set data baris detail (A12 s/d baris TOTAL) agar center-middle secara horizontal & vertikal
                $sheet->getStyle("A12:J$totalRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("N12:O$totalRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

                // 5. TOTAL
                $sheet->setCellValue("A$totalRow", 'TOTAL');
                $sheet->mergeCells("A$totalRow:C$totalRow");
                $sheet->setCellValue("D$totalRow", "=SUM(D12:D$lastRow)");
                $sheet->setCellValue("F$totalRow", "=SUM(F12:F$lastRow)");
                $sheet->setCellValue("G$totalRow", "=SUM(G12:G$lastRow)");
                $sheet->setCellValue("H$totalRow", "=SUM(H12:H$lastRow)");
                $sheet->setCellValue("I$totalRow", "=SUM(I12:I$lastRow)");
                $sheet->setCellValue("J$totalRow", "=SUM(J12:J$lastRow)");

                $sheet->getStyle("A$totalRow:F$totalRow")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
                $sheet->getStyle("G$totalRow")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('00B0F0');
                $sheet->getStyle("H$totalRow")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0000');
                $sheet->getStyle("I$totalRow")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
                $sheet->getStyle("J$totalRow")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('00B050');
                $sheet->getStyle("A12:O$totalRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // 6. COST
                $sheet->mergeCells("A$costRow:C$costRow");
                $sheet->setCellValue("A$costRow", 'TOTAL COST REJECT');
                $sheet->getStyle("A$costRow")->getFont()->setBold(true);
                $sheet->getStyle("A$costRow")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
                
                $sheet->setCellValue("D$costRow", "=F$totalRow * 19000"); 
                $sheet->mergeCells("D$costRow:F$costRow");
                $sheet->getStyle("D$costRow")->getNumberFormat()->setFormatCode('Rp #,##0');
                $sheet->getStyle("D$costRow:F$costRow")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FCE4D6');
                $sheet->getStyle("D$costRow:F$costRow")->getFont()->setBold(true);
                $sheet->getStyle("A$costRow:F$costRow")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                
                // Merapikan alignment khusus cost row agar lurus tengah
                $sheet->getStyle("A$costRow:F$costRow")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("D$costRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // 7. PAGE SETUP
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0); 
                
                // 🛠️ PERBAIKAN TEXTAREA DATA: Set kolom text deskripsi panjang agar wrapText dan lurus VERTICAL_CENTER
                $sheet->getStyle("K12:M$lastRow")->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
            }
        ];
    }
}