<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class StockMovementSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->warn("⚠️ Tidak ada produk. Seed produk dulu.");
            return;
        }

        foreach ($products as $product) {

            // 1. Restock movement (stok bertambah 20)
            DB::table('stock_movements')->insert([
                'product_id'  => $product->id,
                'change'      => 20,
                'source_type' => 'restock',
                'source_id'   => 1, // contoh: id restock order
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            $product->increment('stock', 20);

            // 2. Incoming transaction (stok bertambah 10)
            DB::table('stock_movements')->insert([
                'product_id'  => $product->id,
                'change'      => 10,
                'source_type' => 'transaction_in',
                'source_id'   => 1, // contoh: id transaksi masuk
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            $product->increment('stock', 10);

            // 3. Outgoing transaction (stok berkurang -5)
            DB::table('stock_movements')->insert([
                'product_id'  => $product->id,
                'change'      => -5,
                'source_type' => 'transaction_out',
                'source_id'   => 1, // contoh: id transaksi keluar
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            $product->decrement('stock', 5);
        }

        $this->command->info("✅ StockMovementSeeder selesai. Pergerakan stok berhasil dibuat.");
    }
}
