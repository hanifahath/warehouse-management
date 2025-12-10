<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\StockMovement;

class InventoryService
{
    /**
     * Tambah stok produk.
     */
    public function increaseStock(Product $product, int $quantity): void
    {
        DB::transaction(function () use ($product, $quantity) {
            // Muat ulang produk agar stok terbaru
            $freshProduct = $product->fresh();

            $freshProduct->increment('stock', $quantity);
        });
    }

    /**
     * Kurangi stok produk.
     * Lempar exception jika stok tidak cukup.
     */
    public function decreaseStock(Product $product, int $quantity): void
    {
        DB::transaction(function () use ($product, $quantity) {
            $freshProduct = $product->fresh();

            if ($freshProduct->stock < $quantity) {
                throw new \Exception("Stok produk '{$freshProduct->name}' tidak mencukupi.");
            }

            $freshProduct->decrement('stock', $quantity);
        });
    }

    /**
     * Update stok dengan delta (positif = tambah, negatif = kurangi).
     */
    public function adjustStock(Product $product, int $delta, string $type, $reference = null): void
    {
        DB::transaction(function () use ($product, $delta, $type, $reference) {
            $freshProduct = $product->fresh();

            if ($delta < 0 && $freshProduct->stock + $delta < 0) {
                throw new \Exception("Stok produk '{$freshProduct->name}' tidak mencukupi.");
            }

            $freshProduct->increment('stock', $delta);

            StockMovement::create([
                'product_id' => $freshProduct->id,
                'type' => $type, // 'incoming', 'outgoing', 'adjustment'
                'quantity' => $delta,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference->id ?? null,
                'user_id' => auth()->id(),
            ]);
        });
    }
}