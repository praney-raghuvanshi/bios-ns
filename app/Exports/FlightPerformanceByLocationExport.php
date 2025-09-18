<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
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
            [$forwardRoute, $reverseRoute] = array_pad(explode(':', $routeKey), 2, '');

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
                            $f['flight']['departure_time_local'] ?? '-',
                            $f['atd_local'] ?? '-',
                            $f['formatted_departure_time_diff'] ?? '-',
                            $f['flight']['arrival_time_local'] ?? '-',
                            $f['ata_local'] ?? '-',
                            $f['formatted_arrival_time_diff'] ?? '-',
                            !empty($f['schedule_flight_customers']) ? implode("\r\n", array_column($f['schedule_flight_customers'], 'total_uplifted_weight')) : '-',
                            !empty($f['schedule_flight_remarks']) ? implode("\r\n", array_column($f['schedule_flight_remarks'], 'remark')) : '-',
                        ]);
                    } else {
                        $row = array_merge($row, array_fill(0, 8, '-'));
                    }

                    // Spacer
                    $row[] = '';

                    // Reverse flight
                    if (!empty($flights['reverse_flights'])) {
                        $r = $flights['reverse_flights'][0];
                        $r2 = [];
                        if (isset($flights['reverse_flights'][1])) {
                            $r2 = $flights['reverse_flights'][1];
                        }
                        $row = array_merge($row, [
                            isset($r2['flight']['departure_time_local'])
                                ? implode("\r\n", [$r['flight']['departure_time_local'] ?? '-', $r2['flight']['departure_time_local'] ?? '-'])
                                : ($r['flight']['departure_time_local'] ?? '-'),

                            isset($r2['atd_local'])
                                ? implode("\r\n", [$r['atd_local'] ?? '-', $r2['atd_local'] ?? '-'])
                                : ($r['atd_local'] ?? '-'),

                            isset($r2['formatted_departure_time_diff'])
                                ? implode("\r\n", [$r['formatted_departure_time_diff'] ?? '-', $r2['formatted_departure_time_diff'] ?? '-'])
                                : ($r['formatted_departure_time_diff'] ?? '-'),

                            isset($r2['flight']['arrival_time_local'])
                                ? implode("\r\n", [$r['flight']['arrival_time_local'] ?? '-', $r2['flight']['arrival_time_local'] ?? '-'])
                                : ($r['flight']['arrival_time_local'] ?? '-'),

                            isset($r2['ata_local'])
                                ? implode("\r\n", [$r['ata_local'] ?? '-', $r2['ata_local'] ?? '-'])
                                : ($r['ata_local'] ?? '-'),

                            isset($r2['formatted_arrival_time_diff'])
                                ? implode("\r\n", [$r['formatted_arrival_time_diff'] ?? '-', $r2['formatted_arrival_time_diff'] ?? '-'])
                                : ($r['formatted_arrival_time_diff'] ?? '-'),

                            !empty($r2['schedule_flight_customers'])
                                ? implode("\r\n", [
                                    implode(', ', array_column($r['schedule_flight_customers'], 'total_uplifted_weight')),
                                    '----',
                                    implode(', ', array_column($r2['schedule_flight_customers'], 'total_uplifted_weight'))
                                ])
                                : (!empty($r['schedule_flight_customers'])
                                    ? implode(', ', array_column($r['schedule_flight_customers'], 'total_uplifted_weight'))
                                    : '-'),

                            !empty($r2['schedule_flight_remarks'])
                                ? implode("\r\n", [
                                    implode('; ', array_column($r['schedule_flight_remarks'], 'remark')),
                                    '----',
                                    implode('; ', array_column($r2['schedule_flight_remarks'], 'remark'))
                                ])
                                : (!empty($r['schedule_flight_remarks'])
                                    ? implode('; ', array_column($r['schedule_flight_remarks'], 'remark'))
                                    : '-'),
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
        // === HEADER ROWS (Customer + Weeks) ===
        $sheet->mergeCells("A1:S1");
        $sheet->mergeCells("A2:S2");
        $sheet->mergeCells("A3:S3");

        $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A2")->getFont()->setBold(true)->setSize(12);

        $sheet->getStyle("A1:A3")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $highestRow = $sheet->getHighestRow();

        // === LOOP THROUGH ALL ROWS ===
        for ($row = 4; $row <= $highestRow; $row++) {
            $valueColA = trim((string) $sheet->getCell("A{$row}")->getValue());
            $valueColC = trim((string) $sheet->getCell("C{$row}")->getValue());
            $valueColI = trim((string) $sheet->getCell("I{$row}")->getValue());

            // --- Week title rows ---
            if (strpos($valueColA, 'Week') === 0) {
                $sheet->mergeCells("A{$row}:S{$row}");
                $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle("A{$row}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                    ->setVertical(Alignment::VERTICAL_CENTER);
            }

            // --- Route name rows ---
            // Detect by: col I has "Flight Number"
            if ($valueColI === 'Flight Number') {
                $sheet->mergeCells("C{$row}:I{$row}"); // forward route name
                $sheet->mergeCells("L{$row}:R{$row}"); // reverse route name
                $sheet->getStyle("C{$row}:R{$row}")->getFont()->setBold(true);
                $sheet->getStyle("C{$row}:R{$row}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
            }
        }

        // === GENERAL STYLING ===
        $sheet->getStyle("A1:S{$highestRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle("A1:S{$highestRow}")->getFont()->setSize(10);

        $sheet->getStyle("L1:L{$highestRow}")
            ->getAlignment()->setWrapText(true)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("M1:M{$highestRow}")
            ->getAlignment()->setWrapText(true)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("N1:N{$highestRow}")
            ->getAlignment()->setWrapText(true)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("O1:O{$highestRow}")
            ->getAlignment()->setWrapText(true)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("P1:P{$highestRow}")
            ->getAlignment()->setWrapText(true)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("Q1:Q{$highestRow}")
            ->getAlignment()->setWrapText(true)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("R1:R{$highestRow}")
            ->getAlignment()->setWrapText(true)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // === REMARKS WRAP ===
        // Forward remarks column = J
        $sheet->getStyle("J1:J{$highestRow}")
            ->getAlignment()->setWrapText(true)
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Reverse remarks column = S
        $sheet->getStyle("S1:S{$highestRow}")
            ->getAlignment()->setWrapText(true)
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);
    }

    public function title(): string
    {
        return $this->location;
    }
}
