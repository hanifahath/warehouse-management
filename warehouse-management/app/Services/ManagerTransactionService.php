<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ManagerTransactionService
{
    private const APPROVAL_THRESHOLD = 10000000; // bisa diambil dari config/env

    public function approveTransaction(Transaction $transaction, int $userId): void
    {
        if ($transaction->status !== 'Pending Approval') {
            throw new Exception('Transaksi tidak dalam status Pending Approval atau sudah diproses.');
        }

        $total = $transaction->total_amount ?? $transaction->items->sum(fn($item) => $item->quantity * $item->price_at_transaction);

        if ($total <= self::APPROVAL_THRESHOLD) {
            throw new Exception('Transaksi di bawah ambang — tidak perlu persetujuan Manager.');
        }

        DB::beginTransaction();
        try {
            $transaction->loadMissing('items.product');

            foreach ($transaction->items as $item) {
                $product = $item->product;
                if (!$product) {
                    throw new Exception("Produk ID {$item->product_id} tidak ditemukan.");
                }

                $qty = (int) $item->quantity;
                if (strtolower($transaction->type) === 'outgoing') {
                    if ($product->stock < $qty) {
                        throw new Exception("Stok produk '{$product->name}' tidak mencukupi ({$product->stock} tersedia).");
                    }
                    $product->decrement('stock', $qty);
                    $movementType = 'outgoing';
                } else {
                    $product->increment('stock', $qty);
                    $movementType = 'incoming';
                }

                StockMovement::create([
                    'product_id'     => $product->id,
                    'type'           => $movementType,
                    'quantity'       => $qty,
                    'reference_type' => Transaction::class,
                    'reference_id'   => $transaction->id,
                    'user_id'        => $userId,
                    'reason'         => "Transaction approved - {$movementType}",
                ]);
            }

            $transaction->update([
                'status' => 'Approved',
                'approved_by' => $userId,
                'approved_at' => now(),
            ]);

            DB::commit();
            Log::info("Transaction {$transaction->id} approved by user {$userId}. Total: {$total}");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Error approving transaction {$transaction->id}: {$e->getMessage()}");
            throw new Exception("Gagal menyetujui transaksi: " . $e->getMessage());
        }
    }

    public function rejectTransaction(Transaction $transaction, int $userId): void
    {
        if ($transaction->status !== 'Pending Approval') {
            throw new Exception('Transaksi tidak dalam status Pending Approval atau sudah diproses.');
        }

        $total = $transaction->total_amount ?? $transaction->items->sum(fn($item) => $item->quantity * $item->price_at_transaction);

        if ($total <= self::APPROVAL_THRESHOLD) {
            throw new Exception('Transaksi di bawah ambang — tidak perlu persetujuan Manager.');
        }

        try {
            $transaction->update([
                'status' => 'Rejected',
                'rejected_by' => $userId,
                'rejected_at' => now(),
            ]);

            Log::info("Transaction {$transaction->id} rejected by user {$userId}. Total: {$total}");
        } catch (\Throwable $e) {
            Log::error("Error rejecting transaction {$transaction->id}: {$e->getMessage()}");
            throw new Exception("Gagal menolak transaksi: " . $e->getMessage());
        }
    }
}
