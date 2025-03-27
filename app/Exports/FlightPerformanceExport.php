<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FlightPerformanceExport implements WithMultipleSheets
{
    protected $startWeeks, $endWeeks, $finalData, $customerToShow, $weeksToShow;

    public function __construct($startWeeks, $endWeeks, $finalData, $customerToShow, $weeksToShow)
    {
        $this->startWeeks = $startWeeks;
        $this->endWeeks = $endWeeks;
        $this->finalData = $finalData;
        $this->customerToShow = $customerToShow;
        $this->weeksToShow = $weeksToShow;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->finalData as $data) {
            $sheets[] = new FlightPerformanceByLocationExport($data, $this->startWeeks, $this->endWeeks, $this->customerToShow, $this->weeksToShow);
        }

        return $sheets;
    }
}
