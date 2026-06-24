<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AsakaiReportExport implements FromView, ShouldAutoSize, WithEvents, WithDrawings, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('Produksi.report.asakai.export_excel', $this->data);
    }

    public function title(): string
    {
        return 'Daily Report Asakai ' . $this->data['tanggal'];
    }

    /**
     * LOGO HEADER: IPPI (B1) & Astra (H1)
     */
    public function drawings()
    {
        $drawings = [];
        
        // Logo IPPI (Kolom B)
        $drawing1 = new Drawing();
        $drawing1->setName('Logo IPPI');
        $drawing1->setPath(public_path('images/logo-ippi.png'));
        $drawing1->setHeight(45);
        $drawing1->setCoordinates('B1'); 
        $drawing1->setOffsetX(5);
        $drawings[] = $drawing1;

        // Logo Astra (Kolom H)
        $drawing2 = new Drawing();
        $drawing2->setName('Logo Astra');
        $drawing2->setPath(public_path('images/image.png'));
        $drawing2->setHeight(45);
        $drawing2->setCoordinates('H1');
        $drawing2->setOffsetX(-5);
        $drawings[] = $drawing2;

        return $drawings;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Kolom A dibikin sempit buat padding doang
                $sheet->getColumnDimension('A')->setWidth(3);
                $sheet->getRowDimension(1)->setRowHeight(65);

                // Alignment Vertical untuk semua cell di kolom B-H
                $sheet->getStyle("B1:H$lastRow")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("B1:H$lastRow")->getAlignment()->setWrapText(true);
            },
        ];
    }
}