<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class FlightPerformanceByLocationExport implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $location;
    protected $startWeeks;
    protected $endWeeks;
    protected $customerToShow;
    protected $weeksToShow;

    public function __construct($data, $startWeeks, $endWeeks, $customerToShow, $weeksToShow)
    {
        $this->data = $data;
        $this->location = $data['location'];
        $this->startWeeks = $startWeeks;
        $this->endWeeks = $endWeeks;
        $this->customerToShow = $customerToShow;
        $this->weeksToShow = $weeksToShow;
    }

    public function array(): array
    {
        $data = [];

        // Customer name row
        $data[] = [$this->customerToShow];
        $data[] = ['Weeks : ' . $this->weeksToShow];
        $data[] = ['']; // spacer row

        foreach ($this->data['routes'] as $routeKey => $weeks) {
            [$forwardRoute, $reverseRoute] = explode(':', $routeKey) + ['', ''];

            foreach ($weeks as $week => $days) {
                // Week title row
                $data[] = ['Week ' . $week] + array_fill(1, 18, '');

                // Get first available flight numbers
                $forwardFlightNumber = '';
                $reverseFlightNumber = '';
                foreach ($days as $flights) {
                    if (!$forwardFlightNumber && !empty($flights['flights'][0]['flight']['flight_number'])) {
                        $forwardFlightNumber = $flights['flights'][0]['flight']['flight_number'];
                    }
                    if (!$reverseFlightNumber && !empty($flights['reverse_flights'][0]['flight']['flight_number'])) {
                        $reverseFlightNumber = $flights['reverse_flights'][0]['flight']['flight_number'];
                    }
                }

                // Route name row
                $data[] = [
                    '',
                    '',
                    $forwardRoute,
                    '',
                    '',
                    '',
                    '',
                    '',
                    'Flight Number',
                    $forwardFlightNumber,
                    '', // spacer
                    $reverseRoute,
                    '',
                    '',
                    '',
                    '',
                    '',
                    'Flight Number',
                    $reverseFlightNumber
                ];

                // Column headers row
                $data[] = [
                    'Day',
                    'Date',
                    'STD',
                    'ATD',
                    '+/-',
                    'STA',
                    'ATA',
                    '+/-',
                    'Weight',
                    'Remarks',
                    '',
                    'STD',
                    'ATD',
                    '+/-',
                    'STA',
                    'ATA',
                    '+/-',
                    'Weight',
                    'Remarks'
                ];

                // Day rows
                foreach ($days as $day => $flights) {
                    $row = [
                        Carbon::parse($day)->shortEnglishDayOfWeek,
                        Carbon::parse($day)->format('d'),
                    ];

                    // Forward flight
                    if (!empty($flights['flights'])) {
                        $f = $flights['flights'][0];
                        $row = array_merge($row, [
                            $f['flight']['departure_time'] ?? '-',
                            $f['actual_departure_time'] ?? '-',
                            $f['departure_time_diff'] ?? '-',
                            $f['flight']['arrival_time'] ?? '-',
                            $f['actual_arrival_time'] ?? '-',
                            $f['arrival_time_diff'] ?? '-',
                            !empty($f['schedule_flight_customers']) ? implode("\n", array_column($f['schedule_flight_customers'], 'total_uplifted_weight')) : '-',
                            !empty($f['schedule_flight_remarks']) ? implode("\n", array_column($f['schedule_flight_remarks'], 'remark')) : '-',
                        ]);
                    } else {
                        $row = array_merge($row, array_fill(0, 8, '-'));
                    }

                    // Spacer
                    $row[] = '';

                    // Reverse flight
                    if (!empty($flights['reverse_flights'])) {
                        $r = $flights['reverse_flights'][0];
                        $row = array_merge($row, [
                            $r['flight']['departure_time'] ?? '-',
                            $r['actual_departure_time'] ?? '-',
                            $r['departure_time_diff'] ?? '-',
                            $r['flight']['arrival_time'] ?? '-',
                            $r['actual_arrival_time'] ?? '-',
                            $r['arrival_time_diff'] ?? '-',
                            !empty($r['schedule_flight_customers']) ? implode("\n", array_column($r['schedule_flight_customers'], 'total_uplifted_weight')) : '-',
                            !empty($r['schedule_flight_remarks']) ? implode("\n", array_column($r['schedule_flight_remarks'], 'remark')) : '-',
                        ]);
                    } else {
                        $row = array_merge($row, array_fill(0, 8, '-'));
                    }

                    $data[] = $row;
                }

                // Blank row after each week
                $data[] = array_fill(0, 19, '');
            }
        }

        return $data;
    }


    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        // Bold + center customer & week rows
        $sheet->mergeCells("A1:S1");
        $sheet->mergeCells("A2:S2");
        $sheet->mergeCells("A3:S3");
        $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A2")->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle("A1:A3")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $highestRow = $sheet->getHighestRow();
        for ($row = 4; $row <= $highestRow; $row++) {
            $value = $sheet->getCell("A{$row}")->getValue();

            if (strpos($value, 'Week') === 0) {
                $sheet->mergeCells("A{$row}:S{$row}");
                $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            }

            // Merge route name blocks
            $sheet->mergeCells("C{$row}:I{$row}"); // forward route
            $sheet->mergeCells("L{$row}:R{$row}"); // reverse route
        }

        // Header row styling
        $sheet->getStyle("A1:S{$highestRow}")
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A1:S{$highestRow}")->getFont()->setSize(10);

        $highestRow = $sheet->getHighestRow();

        // Forward remarks column = J
        $sheet->getStyle("J1:J{$highestRow}")->getAlignment()->setWrapText(true);

        // Reverse remarks column = S
        $sheet->getStyle("S1:S{$highestRow}")->getAlignment()->setWrapText(true);
    }


    public function title(): string
    {
        return $this->location;
    }
}
