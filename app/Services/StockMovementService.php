<?php

namespace App\Services;

use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\RestockOrder;
use Illuminate\Support\Facades\DB;

class StockMovementService
{
    /**
     * Update stock from approved transaction
     */
    public static function updateFromTransaction(Transaction $transaction): bool
    {
        return DB::transaction(function () use ($transaction) {
            foreach ($transaction->items as $item) {
                $product = $item->product;
                $beforeQty = $product->current_stock;
                
                if ($transaction->isIncoming()) {
                    // Incoming: add stock (positive change)
                    $product->increment('current_stock', $item->quantity);
                    $change = $item->quantity;
                } else {
                    // Outgoing: subtract stock (negative change)
                    $product->decrement('current_stock', $item->quantity);
                    $change = -$item->quantity;
                }
                
                $afterQty = $product->current_stock;
                
                StockMovement::create([
                    'product_id' => $product->id,
                    'change' => $change,
                    'source_type' => Transaction::class,
                    'source_id' => $transaction->id,
                    'before_qty' => $beforeQty,
                    'after_qty' => $afterQty,
                    'performed_by' => $transaction->approved_by ?? $transaction->creator_id,
                ]);
            }
            
            return true;
        });
    }

    /**
     * Update stock from completed restock order
     */
    public static function updateFromRestock(RestockOrder $restockOrder): bool
    {
        return DB::transaction(function () use ($restockOrder) {
            foreach ($restockOrder->items as $item) {
                $product = $item->product;
                $beforeQty = $product->current_stock;
                
                // Restock always adds stock
                $product->increment('current_stock', $item->quantity);
                
                StockMovement::create([
                    'product_id' => $product->id,
                    'change' => $item->quantity,
                    'source_type' => RestockOrder::class,
                    'source_id' => $restockOrder->id,
                    'before_qty' => $beforeQty,
                    'after_qty' => $product->current_stock,
                    'performed_by' => $restockOrder->manager_id,
                ]);
            }
            
            return true;
        });
    }
    
    /**
     * Get stock history for product
     */
    public static function getProductHistory($productId, $limit = 50)
    {
        return StockMovement::where('product_id', $productId)
            ->with(['performedBy', 'source'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}