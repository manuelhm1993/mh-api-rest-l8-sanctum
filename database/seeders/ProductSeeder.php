<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Usando el Facade DB para crear las querys
        DB::table('products')->insert(
            [
                'name'        => 'Iphone 13',
                'description' => 'Mobile phone apple',
                'amount'      => 980,
            ],
            [
                'name'        => 'Ipad Pro 11',
                'description' => 'Tablet apple',
                'amount'      => 850,
            ],
            [
                'name'        => 'Playstation 5',
                'description' => 'Video console',
                'amount'      => 540,
            ],
        );
    }
}
