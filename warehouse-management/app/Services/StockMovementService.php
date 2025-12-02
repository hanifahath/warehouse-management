<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class StockMovementService
{
    /**
     * Mencatat pergerakan stok dan update stok produk.
     */
    public function record(Product $product, int $quantity, string $type, string $description = null)
    {
        return DB::transaction(function () use ($product, $quantity, $type, $description) {

            if (!in_array($type, ['in', 'out'])) {
                throw new \InvalidArgumentException("Stock movement type harus 'in' atau 'out'");
            }

            // Update stok
            if ($type === 'in') {
                $product->increment('stock', $quantity);
            } else {
                if ($product->stock < $quantity) {
                    throw new \Exception("Stok tidak cukup untuk melakukan pengeluaran.");
                }
                $product->decrement('stock', $quantity);
            }

            // Catat movement
            return StockMovement::create([
                'product_id'  => $product->id,
                'quantity'    => $quantity,
                'type'        => $type,
                'description' => $description,
            ]);
        });
    }
}
