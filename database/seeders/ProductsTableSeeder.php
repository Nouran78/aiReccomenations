<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('products')->insert([
            [
                'name' => 'Iced Coffee',
                'base_price' => 150,
                'category' => 'cold',
            ],
            [
                'name' => 'Hot Chocolate',
                'base_price' => 100,
                'category' => 'hot',
            ],
            [
                'name' => 'Lemonade',
                'base_price' => 80,
                'category' => 'cold',
            ],
            [
                'name' => 'Espresso',
                'base_price' => 90,
                'category' => 'hot',
            ],
        ]);
    }
}
