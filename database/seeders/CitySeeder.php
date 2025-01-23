<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('cities')->insert([
            ['name' => 'Bihać'],
            ['name' => 'Cazin'],
            ['name' => 'Velika Kladuša'],
            ['name' => 'Bužim'],
            ['name' => 'Banja Luka'],
            ['name' => 'Sarajevo'],
            ['name' => 'Mostar'],
        ]);
    }
}
