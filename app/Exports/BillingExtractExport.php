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
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\DefaultValueBinder;

class BillingExtractExport extends DefaultValueBinder implements FromArray, WithTitle, ShouldAutoSize, WithStyles, WithColumnFormatting, WithCustomValueBinder
{
    protected $data;
    protected $title = 'Billing Extract';

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
            'Customer',  // A
            'Flight #',  // B
            'Date',  // C
            'Airwaybill #',  // D
            'Origin',  // E
            'End Dest',  // F
            'Product',  // G
            'Actual',  // H
            'Volumetric',  // I
            'Total Actual',  // J
            'Total Volumetric',  // K
            'First/Subs',  // L
        ];

        // ========================
        // DATA ROWS
        // ========================
        foreach ($this->data as $datum) {
            $rows[] = [
                $datum['customer'],
                $datum['flight'],
                ExcelDate::PHPToExcel(Carbon::parse($datum['raw_date'])),
                $datum['awb'],
                $datum['origin'],
                $datum['end_destination'],
                $datum['product'],
                $datum['actual'],
                $datum['volume'],
                $datum['total_actual'],
                $datum['total_volume'],
                $datum['shipment_type']
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('C')->getNumberFormat()->setFormatCode('dd-mm-yyyy');
        // Style headers
        $sheet->getStyle("A1:L1")->getFont()->setBold(true)->setSize(12);
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'D' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function bindValue(Cell $cell, $value): bool
    {
        if ($cell->getColumn() === 'D') { // Airwaybill #
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function title(): string
    {
        return $this->title;
    }
}
