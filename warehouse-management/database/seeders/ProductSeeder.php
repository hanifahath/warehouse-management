<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::firstOrCreate([
            'sku' => 'LAP-X200'
        ], [
            'name' => 'Laptop',
            'category_id' => 1,
            'purchase_price' => 8000000,
            'selling_price' => 9000000,
            'unit' => 'pcs',
            'min_stock' => 5,
            'current_stock' => 15,
            'rack_location' => 'R-A1',
        ]);

        Product::firstOrCreate([
            'sku' => 'CHR-OFC-01'
        ], [
            'name' => 'Office Chair',
            'category_id' => 2,
            'purchase_price' => 300000,
            'selling_price' => 450000,
            'unit' => 'pcs',
            'min_stock' => 2,
            'current_stock' => 8,
            'rack_location' => 'R-B3',
        ]);

        Product::firstOrCreate([
            'sku' => 'FOD-IND-10'
        ], [
            'name' => 'Instant Noodles',
            'category_id' => 3,
            'purchase_price' => 2500,
            'selling_price' => 3500,
            'unit' => 'pack',
            'min_stock' => 10,
            'current_stock' => 50,
            'rack_location' => 'S-C4',
        ]);
    }
}
