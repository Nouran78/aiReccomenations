<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('orders')->insert([
            [
                'product_id' => 1,
                'quantity' => 2,
                'price' => 150,
                'adjusted_price' => 0,
                'price_reason' => 'null',
                'type' => 'cold',
            ],
            [
                'product_id' => 2,
                'quantity' => 1,
                'price' => 100,
                'adjusted_price' => 0,
                'price_reason' => null,
                'type' => 'hot',
            ],
            [
                'product_id' => 3,
                'quantity' => 5,
                'price' => 80,
                'adjusted_price' => 90,
                'price_reason' => 'hot weather',
                'type' => 'cold',
            ],
        ]);
    }
}

