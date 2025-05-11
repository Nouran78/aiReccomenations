<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
class SalesSummarySeeder extends Seeder
{
    public function run()
    {
        DB::table('sales_summary')->insert([
            [
                'product_id' => 1,
                'total_quantity' => 2,
                'total_revenue' => 300,
                'adjusted_total_revenue' => 0,
                'orders_count' => 1,
                'last_min_revenue' => 300,
                'last_min_orders' => 1,
                'revenue_change_pct' => 0,
                'adjusted_revenue_change_pct' => 0,
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'product_id' => 2,
                'total_quantity' => 1,
                'total_revenue' => 100,
                'adjusted_total_revenue' => 0,
                'orders_count' => 1,
                'last_min_revenue' => 100,
                'last_min_orders' => 1,
                'revenue_change_pct' => 0,
                'adjusted_revenue_change_pct' => 0,
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'product_id' => 3,
                'total_quantity' => 5,
                'total_revenue' => 400,
                'adjusted_total_revenue' => 450,
                'orders_count' => 1,
                'last_min_revenue' => 400,
                'last_min_orders' => 1,
                'revenue_change_pct' => 0,
                'adjusted_revenue_change_pct' => 0,
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
        ]);
    }
}
