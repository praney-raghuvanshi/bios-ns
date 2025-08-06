<?php

namespace App\Console\Commands;

use App\Models\CustomerEmail;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class ImportExcelToDB extends Command
{
    protected $signature = 'import:excel {file}';
    protected $description = 'One-time import of Excel file with multiple sheets into DB';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: $filePath");
            return;
        }

        $spreadsheet = IOFactory::load($filePath);
        //$sheetCount = $spreadsheet->getSheetCount();

        $sheet = $spreadsheet->getSheet(27);
        $sheetName = $sheet->getTitle();

        $this->info("Processing sheet: $sheetName");

        // $rows = $sheet->toArray(null, true, true, true);

        // foreach ($rows as $row) {

        //     if (empty($row['A']) || empty($row['B'])) {
        //         continue; // Skip rows with empty required fields
        //     }

        //     $name = trim($row['A']);
        //     $email = trim($row['B']);

        //     $customerEmail = CustomerEmail::firstOrCreate(
        //         ['customer_id' => 73, 'email' => $email],
        //         ['name' => $name, 'added_by' => 1]
        //     );

        //     $exists = DB::table('customer_email_location')
        //         ->where('customer_email_id', $customerEmail->id)
        //         ->where('location_id', 29)
        //         ->exists();

        //     if (!$exists) {
        //         DB::table('customer_email_location')->insert([
        //             'customer_email_id' => $customerEmail->id,
        //             'location_id' => 29,
        //         ]);
        //     }

        //     $this->info("Imported: {$name} - {$email}");
        // }

        $this->info("Import complete.");
    }
}
