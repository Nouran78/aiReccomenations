<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
class CreateSalesSummaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
        CREATE TABLE sales_summary (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            product_id INTEGER NOT NULL UNIQUE,
            total_quantity INTEGER DEFAULT 0,
            total_revenue REAL DEFAULT 0,
            adjusted_total_revenue REAL DEFAULT 0,   -- New column for adjusted revenue
            orders_count INTEGER DEFAULT 0,
            last_min_revenue REAL DEFAULT 0,
            last_min_orders INTEGER DEFAULT 0,
            revenue_change_pct REAL DEFAULT 0,
            adjusted_revenue_change_pct REAL DEFAULT 0, -- Track adjusted revenue change percentage
            updated_at TEXT DEFAULT CURRENT_TIMESTAMP
        )
    ');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP TABLE IF EXISTS sales_summary');
    }
}
