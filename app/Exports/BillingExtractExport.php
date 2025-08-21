<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class BillingExtractExport implements FromArray, WithTitle, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected $data;
    protected $title = 'Billing Extract';

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'J' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $rows = [];

        // ========================
        // HEADER ROW 1
        // ========================
        $rows[] = [
            '',  // A
            '',  // B
            '',  // C
            '',  // D
            '',  // E
            '',  // F
            '',  // G
            '',  // H
            '',  // I
            '',  // J
            '',  // K
            'CON1',  // L
            '',      // M
            'CON2',  // N
            '',      // O
            'CON3',  // P
            '',      // Q
            'CON4',  // R
            '',      // S
            'Declared', // T
            '',         // U
            'Actual',   // V
            'Volume',   // W
            '',         // X
            'Total Actual', // Y
            '',             // Z
            'Total Volume', // AA
            'First/Subs',   // AB
            '',             // AC
            'CUSTOMER DETAIL', // AD (merged AD:AH)
            '',      // AE
            '',      // AF
            '',      // AG
            '',      // AH
            '',      // AI
            'CHARGES' // AJ
        ];

        // ========================
        // HEADER ROW 2
        // ========================
        $rows[] = [
            'DATE',  // A
            '',      // B
            'ORIGIN', // C
            '',      // D
            'DEST',  // E
            '',      // F
            'END DEST', // G
            'FLIGHT#',  // H
            '',      // I
            'AIRWAY BILL #', // J
            '',      // K
            'LD3',   // L
            '',      // M
            'LD7',   // N
            '',      // O
            '',      // P
            '',      // Q
            '',      // R
            '',      // S
            'KG',    // T
            '',      // U
            'KG',    // V
            'KG',    // W
            '',      // X
            'KG',    // Y
            '',      // Z
            'KG',    // AA
            'SHIPMENT', // AB
            '',      // AC
            'CON#',  // AD
            '',      // AE
            'CUSTOMER', // AF
            '',      // AG
            'PRODUCT', // AH
            '',
            'HANDLING' // AJ
        ];

        // ========================
        // DATA ROWS
        // ========================
        foreach ($this->data as $datum) {
            $rows[] = [
                ExcelDate::PHPToExcel(Carbon::parse($datum['raw_date'])),   // A
                '',               // B
                $datum['origin'], // C
                '',               // D
                $datum['destination'], // E
                '',               // F
                $datum['end_destination'], // G
                $datum['flight'], // H
                '',               // I
                (string) $datum['awb'],    // J
                '',               // K
                '',               // L (LD3 - not mapped yet)
                '',               // M
                '',               // N (LD7 - not mapped yet)
                '',               // O
                '',               // P
                '',               // Q
                '',               // R
                '',               // S
                $datum['declared'], // T
                '',                 // U
                $datum['actual'],   // V
                $datum['volume'],   // W
                '',                 // X
                $datum['total_actual'], // Y
                '',                     // Z
                $datum['total_volume'], // AA
                $datum['shipment_type'], // AB
                '',                     // AC
                '',                     // AD (CON#)
                '',                     // AE
                $datum['customer'],     // AF
                '',                     // AG
                $datum['product'],      // AH
                '',
                ''                      // AJ (Handling)
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // Merge CUSTOMER DETAIL across AD:AH (Row 1 only)
        $sheet->mergeCells("AD1:AH1");

        $sheet->getStyle('A')->getNumberFormat()->setFormatCode('dd-mm-yyyy');

        // Style headers
        $sheet->getStyle("A1:AJ1")->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle("A2:AJ2")->getFont()->setBold(true)->setSize(12);

        $sheet->getStyle("A1:AJ2")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Hide skipped columns
        foreach (['B', 'D', 'F', 'I', 'K', 'M', 'O', 'Q', 'S', 'U', 'X', 'Z', 'AC', 'AE', 'AG', 'AI'] as $col) {
            $sheet->getColumnDimension($col)->setVisible(false);
        }
    }

    public function title(): string
    {
        return $this->title;
    }
}
