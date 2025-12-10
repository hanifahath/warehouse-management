<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ManagerTransactionService
{
    /**
     * Verify Incoming Transaction (Barang Masuk)
     * Status: Pending → Verified
     * Stok bertambah
     */
    public function verifyTransaction(Transaction $transaction, $verifierId)
    {
        return DB::transaction(function () use ($transaction, $verifierId) {
            // Update transaction status
            $transaction->update([
                'status' => 'Verified',
                'approved_by' => $verifierId,
                'approved_at' => now(),
            ]);

            // Update stock for each item (tambah stok)
            foreach ($transaction->items as $item) {
                $product = $item->product;
                $product->increment('current_stock', $item->quantity);
                
                // Log stock movement
                $this->logStockMovement(
                    $product,
                    $item->quantity,
                    'incoming',
                    $transaction->id,
                    "Verified incoming transaction #{$transaction->transaction_number}"
                );
            }

            return $transaction;
        });
    }

    /**
     * Approve Outgoing Transaction (Barang Keluar)
     * Status: Pending → Approved
     * Stok berkurang
     */
    public function approveTransaction(Transaction $transaction, $approverId)
    {
        return DB::transaction(function () use ($transaction, $approverId) {
            // Validate stock availability
            foreach ($transaction->items as $item) {
                $product = $item->product;
                if ($product->current_stock < $item->quantity) {
                    throw new \Exception(
                        "Stok {$product->name} tidak mencukupi. " .
                        "Stok tersedia: {$product->current_stock}, " .
                        "Dibutuhkan: {$item->quantity}"
                    );
                }
            }

            // Update transaction status
            $transaction->update([
                'status' => 'Approved',
                'approved_by' => $approverId,
                'approved_at' => now(),
            ]);

            // Update stock for each item (kurangi stok)
            foreach ($transaction->items as $item) {
                $product = $item->product;
                $product->decrement('current_stock', $item->quantity);
                
                // Log stock movement
                $this->logStockMovement(
                    $product,
                    $item->quantity * -1, // negative for outgoing
                    'outgoing',
                    $transaction->id,
                    "Approved outgoing transaction #{$transaction->transaction_number}"
                );
            }

            return $transaction;
        });
    }

    /**
     * Reject Transaction (kedua type)
     * Status: Pending → tetap Pending? Atau ada status khusus?
     * Tidak ada perubahan stok
     */
    public function rejectTransaction(Transaction $transaction, $rejecterId, $reason = null)
    {
        return DB::transaction(function () use ($transaction, $rejecterId, $reason) {
            // Update transaction status to Rejected
            $transaction->update([
                'status' => 'Rejected', // Butuh status Rejected di enum
                'notes' => ($transaction->notes ?? '') . "\n[DITOLAK] " . now()->format('Y-m-d H:i') . 
                        " oleh User #{$rejecterId}: {$reason}",
            ]);

            return $transaction;
        });
    }

    /**
     * Helper: Log stock movement
     */
    private function logStockMovement($product, $quantity, $type, $transactionId, $notes)
    {
        // Implementasi tabel stock_movements jika ada
        // atau gunakan activity log
        \Log::info("Stock Movement - Product: {$product->id}, " .
                  "Quantity: {$quantity}, Type: {$type}, " .
                  "Transaction: {$transactionId}, Notes: {$notes}");
    }

    /**
     * Get transaction statistics for dashboard
     */
    public function getApprovalStats()
    {
        return [
            'pending' => Transaction::where('status', 'Pending')->count(),
            'pending_incoming' => Transaction::where('status', 'Pending')
                ->where('type', 'Incoming')->count(),
            'pending_outgoing' => Transaction::where('status', 'Pending')
                ->where('type', 'Outgoing')->count(),
            'verified_today' => Transaction::where('status', 'Verified')
                ->whereDate('approved_at', today())
                ->count(),
            'approved_today' => Transaction::where('status', 'Approved')
                ->whereDate('approved_at', today())
                ->count(),
        ];
    }
}