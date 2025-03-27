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

        // Add Customer Name as the first row
        $data[] = [$this->customerToShow];
        $data[] = ['Weeks : ' . $this->weeksToShow];
        $data[] = [''];
        $data[] = ['Day', 'Date', 'STD', 'ATD', '+/-', 'STA', 'ATA', '+/-', 'Weight', 'Remarks', '', 'STD', 'ATD', '+/-', 'STA', 'ATA', '+/-', 'Weight', 'Remarks'];

        foreach ($this->data['routes'] as $routeKey => $weeks) {
            // Extract Forward and Reverse routes from the route key
            [$forwardRoute, $reverseRoute] = explode(':', $routeKey) + ['', ''];

            $forwardFlightNumber = '';
            $reverseFlightNumber = '';

            foreach ($weeks as $weekData) {
                foreach ($weekData as $flightData) {
                    if (!empty($flightData['flights']) && isset($flightData['flights'][0]['flight'])) {
                        $forwardFlightNumber = $flightData['flights'][0]['flight']['flight_number'];
                    }
                    if (!empty($flightData['reverse_flights']) && isset($flightData['reverse_flights'][0]['flight'])) {
                        $reverseFlightNumber = $flightData['reverse_flights'][0]['flight']['flight_number'];
                    }
                    if ($forwardFlightNumber !== '' && $reverseFlightNumber !== '') {
                        break 2; // Break out of both loops once flight numbers are found
                    }
                }
            }

            foreach ($weeks as $week => $days) {

                $data[] = ['', '', $forwardRoute, '', '', '', 'Flight Number', $forwardFlightNumber, '', '', '', $reverseRoute, '', '', '', 'Flight Number', $reverseFlightNumber, '', ''];

                // Add a row indicating the week
                $data[] = ['Week' => 'Week ' . $week];

                foreach ($days as $day => $flights) {
                    // Prepare row structure for each day
                    $row = [
                        Carbon::parse($day)->shortEnglishDayOfWeek,
                        Carbon::parse($day)->format('d'),
                    ];

                    // 🔹 **Forward Flight Data**
                    if (!empty($flights['flights'])) {
                        $flight = $flights['flights'][0]; // Assuming one flight per day
                        $row = array_merge($row, [
                            $flight['flight']['departure_time'] ?? '-',
                            $flight['actual_departure_time'] ?? '-',
                            $flight['departure_time_diff'] ?? '-',
                            $flight['flight']['arrival_time'] ?? '-',
                            $flight['actual_arrival_time'] ?? '-',
                            $flight['arrival_time_diff'] ?? '-',
                            !empty($flight['schedule_flight_customers'])
                                ? implode(', ', array_column($flight['schedule_flight_customers'], 'total_uplifted_weight'))
                                : '-',
                            !empty($flight['schedule_flight_remarks'])
                                ? implode(', ', array_column($flight['schedule_flight_remarks'], 'remark'))
                                : '-',
                            ''
                        ]);
                    } else {
                        // Empty row for missing forward flights
                        $row = array_merge($row, [
                            '-',
                            '-',
                            '-',
                            '-',
                            '-',
                            '-',
                            '-',
                            '-',
                            ''
                        ]);
                    }

                    // 🔹 **Reverse Flight Data**
                    if (!empty($flights['reverse_flights'])) {
                        $flight = $flights['reverse_flights'][0]; // Assuming one reverse flight per day
                        $row = array_merge($row, [
                            $flight['flight']['departure_time'] ?? '-',
                            $flight['actual_departure_time'] ?? '-',
                            $flight['departure_time_diff'] ?? '-',
                            $flight['flight']['arrival_time'] ?? '-',
                            $flight['actual_arrival_time'] ?? '-',
                            $flight['arrival_time_diff'] ?? '-',
                            !empty($flight['schedule_flight_customers'])
                                ? implode(', ', array_column($flight['schedule_flight_customers'], 'total_uplifted_weight'))
                                : '-',
                            !empty($flight['schedule_flight_remarks'])
                                ? implode(', ', array_column($flight['schedule_flight_remarks'], 'remark'))
                                : '-',
                        ]);
                    } else {
                        // Empty row for missing reverse flights
                        $row = array_merge($row, [
                            '-',
                            '-',
                            '-',
                            '-',
                            '-',
                            '-',
                            '-',
                            '-',
                        ]);
                    }

                    // 🔹 Add row to data array
                    $data[] = $row;
                }
            }
            $data[] = [''];
            $data[] = [''];
        }


        return $data;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        // 🔹 Merge Customer Name Cell Across 20 Columns and Bold
        $sheet->mergeCells("A1:S1");
        $sheet->mergeCells("A2:S2");
        $sheet->mergeCells("A3:S3");
        $sheet->mergeCells("A6:S6");
        $sheet->mergeCells("C5:F5");
        $sheet->mergeCells("H5:J5");
        $sheet->mergeCells("L5:O5");
        $sheet->mergeCells("Q5:S5");
        $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A2")->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle("A6")->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle("A1")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A2")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A4:S4")->getFont()->setBold(true);
        $sheet->getStyle("A4:S4")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A5:S5")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A5:S5")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function title(): string
    {
        return $this->location;
    }
}
