<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {        
        $csv = fopen('database\data\Dataset-emisi-karbon.csv', 'r');
        if($csv === false){
            die("Error opening CSV file");
        }

        $headers = fgetcsv($csv);
        $headers = array_map('trim', $headers);

        DB::table('emission')->truncate();

        while (($row = fgetcsv($csv)) !== FALSE) {
            if (count($row) !== count($headers)) {
                continue;
            }

            DB::table('emission')->insert([
                'vehicle_type' => $row[0],
                'avarage_emission' => $row[1],
            ]);
        }

        fclose($csv);
    }
}
