<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RestockItem;

class RestockItemSeeder extends Seeder
{
    public function run(): void
    {
        RestockItem::firstOrCreate([
            'restock_order_id' => 1,
            'product_id' => 1,
        ], [
            'quantity' => 5,
        ]);
    }
}
