<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PublicTransportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('publictransports')->truncate();

        Schema::enableForeignKeyConstraints();

        DB::table('publictransports')->insert([
            ['name' => 'MRT', 'base_price' => 3000, 'price_increase' => 1000, 'emission_factor_pkm' => 0.015], // 12 stops, +1000 per stop, max 14000
            ['name' => 'KRL', 'base_price' => 3000, 'price_increase' => 1000, 'emission_factor_pkm' => 0.025], // +1000 per 10KM setelah 25 KM 
            ['name' => 'TransJakarta', 'base_price' => 3500, 'price_increase' => 0, 'emission_factor_pkm' => 0.015], 
            ['name' => 'Gojek Motor', 'base_price' => 9000, 'price_increase' => 2000, 'emission_factor_pkm' => 0], // +2000 per 1km setelah 3 km
            ['name' => 'Grab Motor', 'base_price' => 12000, 'price_increase' => 2000, 'emission_factor_pkm' => 0], // +2500 per 1km setelah 3 km
            ['name' => 'Gojek Mobil', 'base_price' => 25000, 'price_increase' => 2000, 'emission_factor_pkm' => 0], // +2000 per 1km setelah 3 km
            ['name' => 'Grab Mobil', 'base_price' => 23000, 'price_increase' => 2500, 'emission_factor_pkm' => 0], // +2000 per 1km setelah 3 km
        ]);
    }
}
