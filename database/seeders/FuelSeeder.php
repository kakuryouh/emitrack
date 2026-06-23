<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FuelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('fuels')->truncate();

        Schema::enableForeignKeyConstraints();

        DB::table('fuels')->insert([
            ['fuel_name' => 'Pertalite', 'ncv' => 44.61, 'carbon_emission_factor' => 69.29, 'density' => 0.715, 'price' => 10000],
            ['fuel_name' => 'Pertamax', 'ncv' => 44.61, 'carbon_emission_factor' => 69.04, 'density' => 0.715, 'price' => 16250],
            ['fuel_name' => 'Pertamax Turbo', 'ncv' => 44.62, 'carbon_emission_factor' => 68.91, 'density' => 0.715, 'price' => 20750],
            ['fuel_name' => 'Pertamax Dex', 'ncv' => 43.55, 'carbon_emission_factor' => 72.85, 'density' => 0.815, 'price' => 24800],
            ['fuel_name' => 'Dexlite', 'ncv' => 43.43, 'carbon_emission_factor' => 72.93, 'density' => 0.815, 'price' => 23000],
            ['fuel_name' => 'Solar', 'ncv' => 43.27, 'carbon_emission_factor' => 73.28, 'density' => 0.815, 'price' => 6800],
        ]);
    }
}
