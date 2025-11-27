<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductWarehouseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product_warehouse')->insert([
            'product_id' => 1,
            'warehouse_id' => 1,
            'stock' => 10,
        ]);
    }
}
