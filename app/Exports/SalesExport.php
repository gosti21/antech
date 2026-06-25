<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SalesExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected $sales;
    protected $totalSales;
    protected $dateFrom;
    protected $dateTo;

    public function __construct(Collection $sales, $totalSales, $dateFrom, $dateTo)
    {
        $this->sales = $sales;
        $this->totalSales = $totalSales;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function collection()
    {
        return $this->sales->map(function ($sale, $index) {
            return [
                'N°' => $index + 1,
                'Nro. Orden' => $sale['order_number'],
                'Fecha' => $sale['date'],
                'Cliente' => $sale['customer'],
                'Método de Pago' => $sale['payment_methods'],
                'Subtotal S/.' => number_format($sale['subtotal'], 2),
                'IGV S/.' => number_format($sale['igv'], 2),
                'Total S/.' => number_format($sale['total'], 2),
                'Estado' => ucfirst($sale['status']),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'N°',
            'Nro. Orden',
            'Fecha',
            'Cliente',
            'Método de Pago',
            'Subtotal S/.',
            'IGV S/.',
            'Total S/.',
            'Estado'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo del encabezado
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '10B981'], // Verde
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Estilo de las celdas de datos
        $lastRow = $this->sales->count() + 1;
        $sheet->getStyle('A2:I' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Centrar columnas específicas
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:I' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // N°
            'B' => 15,  // Nro. Orden
            'C' => 18,  // Fecha
            'D' => 30,  // Cliente
            'E' => 20,  // Método de Pago
            'F' => 15,  // Subtotal
            'G' => 12,  // IGV
            'H' => 15,  // Total
            'I' => 15,  // Estado
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $this->sales->count() + 2;

                // Agregar fila de TOTAL
                $event->sheet->setCellValue('G' . $lastRow, 'TOTAL VENTAS:');
                $event->sheet->setCellValue('H' . $lastRow, 'S/. ' . number_format($this->totalSales, 2));

                // Estilo del total
                $event->sheet->getStyle('G' . $lastRow . ':H' . $lastRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 13,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '3B82F6'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                    ],
                ]);
            },
        ];
    }
}
