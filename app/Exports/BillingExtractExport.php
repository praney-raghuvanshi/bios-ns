<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BillingExtractExport implements FromArray, WithTitle, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $title = 'Billing Extract';

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $rows[] = [
            '',
            '',
            '',
            '',
            '',
            '',
            'CON1',
            'CON2',
            'CON3',
            'CON4',
            'Declared',
            'Actual',
            'Volume',
            'Total Actual',
            'Total Volume',
            'First/Subs',
            'CUSTOMER DETAIL',
            '',
            '',
            'CHARGES'
        ];

        $rows[] = ['DATE', 'ORIGIN', 'DEST', 'END DEST', 'FLIGHT #', 'AIRWAY BILL #', 'LD3', 'LD7', '', '', 'KG', 'KG', 'KG', 'KG', 'KG', 'Shipment', 'CON #', 'CUSTOMER', 'PRODUCT', 'HANDLING'];

        foreach ($this->data as $datum) {
            $rows[] = [
                $datum['date'],
                $datum['origin'],
                $datum['destination'],
                $datum['end_destination'],
                $datum['flight'],
                $datum['awb'],
                '',
                '',
                '',
                '',
                $datum['declared'],
                $datum['actual'],
                $datum['volume'],
                $datum['total_actual'],
                $datum['total_volume'],
                $datum['shipment_type'],
                '',
                $datum['customer'],
                $datum['product'],
                ''
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells("A1:F1");
        $sheet->mergeCells("Q1:S1");

        $sheet->getStyle("A1:T1")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A1:T1")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A2:T2")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A2:T2")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function title(): string
    {
        return $this->title;
    }
}
