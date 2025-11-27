<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan Category dengan ID 1, 2, 3 sudah ada sebelum menjalankan seeder ini.
        
        Product::create([
            'name' => 'Laptop',
            'sku' => 'LAP-X200', // WAJIB: Tambahkan SKU unik
            'category_id' => 1,
            'purchase_price' => 8000000,
            'selling_price' => 9000000,
            'unit' => 'pcs',
            'min_stock' => 5,
            'stock' => 15, // Opsional: Tambahkan stok awal untuk testing
            'location' => 'R-A1', // Opsional: Tambahkan lokasi rak
        ]);

        Product::create([
            'name' => 'Office Chair',
            'sku' => 'CHR-OFC-01', // WAJIB: Tambahkan SKU unik
            'category_id' => 2,
            'purchase_price' => 300000,
            'selling_price' => 450000,
            'unit' => 'pcs',
            'min_stock' => 2,
            'stock' => 8, // Stok awal
            'location' => 'R-B3',
        ]);

        Product::create([
            'name' => 'Instant Noodles',
            'sku' => 'FOD-IND-10', // WAJIB: Tambahkan SKU unik
            'category_id' => 3,
            'purchase_price' => 2500,
            'selling_price' => 3500,
            'unit' => 'pack',
            'min_stock' => 10,
            'stock' => 50, // Stok awal
            'location' => 'S-C4',
        ]);
    }
}