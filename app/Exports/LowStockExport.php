<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LowStockExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $items;

    public function __construct(Collection $items)
    {
        $this->items = $items;
    }

    public function collection()
    {
        return $this->items->map(function ($item, $index) {
            return [
                'N°' => $index + 1,
                'SKU' => $item['sku'],
                'Producto' => $item['product_name'],
                'Marca' => $item['brand'],
                'Stock Actual' => $item['current_stock'],
                'Stock Mínimo' => $item['min_stock'],
                'Precio S/.' => number_format($item['selling_price'], 2),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'N°',
            'SKU',
            'Producto',
            'Marca',
            'Stock Actual',
            'Stock Mínimo',
            'Precio S/.'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo del encabezado
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3B82F6'], // Azul
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
        $lastRow = $this->items->count() + 1;
        $sheet->getStyle('A2:G' . $lastRow)->applyFromArray([
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

        // Centrar las columnas numéricas
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E2:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // N°
            'B' => 15,  // SKU
            'C' => 40,  // Producto
            'D' => 20,  // Marca
            'E' => 15,  // Stock Actual
            'F' => 15,  // Stock Mínimo
            'G' => 15,  // Precio
        ];
    }
}
