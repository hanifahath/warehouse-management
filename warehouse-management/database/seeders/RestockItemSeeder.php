<?php

namespace Database\Seeders;

use App\Models\RestockItem;
use Illuminate\Database\Seeder;

class RestockItemSeeder extends Seeder
{
    public function run(): void
    {
        RestockItem::create([
            'restock_order_id' => 1,
            'product_id' => 1,
            'quantity' => 5,
        ]);
    }
}
