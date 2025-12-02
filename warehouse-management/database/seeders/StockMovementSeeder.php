<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\User;

class StockMovementSeeder extends Seeder
{
    public function run(): void
    {
        $staff = User::where('role','Staff')->first();
        foreach (Product::all() as $product) {

            // Simulasi restock
            StockMovement::firstOrCreate([
                'product_id' => $product->id,
                'source_type' => 'restock',
                'source_id' => 1,
            ], [
                'change' => 20,
                'before_qty' => $product->current_stock,
                'after_qty' => $product->current_stock + 20,
                'performed_by' => $staff->id,
            ]);
            $product->increment('current_stock', 20);

            // Incoming transaction
            StockMovement::firstOrCreate([
                'product_id' => $product->id,
                'source_type' => 'transaction_in',
                'source_id' => 1,
            ], [
                'change' => 10,
                'before_qty' => $product->current_stock,
                'after_qty' => $product->current_stock + 10,
                'performed_by' => $staff->id,
            ]);
            $product->increment('current_stock', 10);

            // Outgoing transaction
            StockMovement::firstOrCreate([
                'product_id' => $product->id,
                'source_type' => 'transaction_out',
                'source_id' => 1,
            ], [
                'change' => -5,
                'before_qty' => $product->current_stock,
                'after_qty' => $product->current_stock - 5,
                'performed_by' => $staff->id,
            ]);
            $product->decrement('current_stock', 5);
        }
    }
}
