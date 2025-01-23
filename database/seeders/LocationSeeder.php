<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bihac_id = DB::table('cities')->where('name', 'Bihać')->value('id');
        $cazin_id = DB::table('cities')->where('name', 'Cazin')->value('id');

        DB::table('locations')->insert([
            [
                'name' => 'HSC Arena',
                'city_id' => $bihac_id,
                'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRzAFGu-Q23C35W167sKk_2tNnflri_uJiS9w3mIY5Yth9ytJcj4s72RV9F6rogCCnjWt4&usqp=CAU'
            ],
            [
                'name' => 'Dvorana Luke',
                'city_id' => $bihac_id,
                'image' => 'https://uskvijesti.ba/wp-content/uploads/2023/12/IMG_6946.jpeg'
            ],
            [
                'name' => 'Dvorana Luke - Plava Sala',
                'city_id' => $bihac_id,
                'image' => 'https://www.vijesti.ba/fajlovi/news/do-kraja-godine-ocekuje-se-otvaranje-sportske-dvorane-luke-u-bihacu-bihacdvorana_64c4d2523416e.jpg?size=lg'
            ],
            [
                'name' => 'Tehnička Sala',
                'city_id' => $bihac_id,
                'image' => 'https://etsbi.edu.ba/wp-content/uploads/2021/05/safe_image.php_-3.jpg'
            ],
            [
                'name' => 'Gimnazija Cazin',
                'city_id' => $cazin_id,
                'image' => 'https://sarajevo.travel/assets/photos/places/original/grbavica-sports-hall-1466163808.jpg'
            ],
            [
                'name' => 'Sportska Dvorana Salih Omerčević',
                'city_id' => $cazin_id,
                'image' => 'https://sarajevo.travel/assets/photos/places/original/grbavica-sports-hall-1466163808.jpg'
            ],
        ]);
    }
}
