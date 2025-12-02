<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Services\StockMovementService;

class TransactionService
{
    private $stockMovementService;

    // Threshold harus diambil dari tempat yang bisa dikonfigurasi, bukan hardcode
    private const APPROVAL_THRESHOLD = 10000000; 

    public function __construct(StockMovementService $stockMovementService)
    {
        $this->stockMovementService = $stockMovementService;
    }

    public function calculateTotalAmount(array $items): int
    {
        $total = 0;
        foreach ($items as $item) {
            $total += ($item['quantity'] ?? 0) * ($item['price_at_transaction'] ?? 0);
        }
        return $total;
    }

    private function determineStatus(int $totalAmount): string
    {
        return $totalAmount > self::APPROVAL_THRESHOLD ? 'Pending Approval' : 'Completed';
    }

    public function createTransaction(array $data, string $type): Transaction
    {
        $totalAmount = $this->calculateTotalAmount($data['items']);
        $status = $this->determineStatus($totalAmount);
        $movementType = strtolower($type);
        $userId = auth()->id();

        DB::beginTransaction();
        try {
            // Validasi Stok Awal untuk Outgoing yang Completed (Auto-approve)
            if ($type === 'Outgoing' && $status === 'Completed') {
                $this->validateOutgoingStock($data['items']);
            }

            $transaction = Transaction::create([
                'transaction_number' => ($type === 'Incoming' ? 'IN-' : 'OUT-') . strtoupper(Str::random(10)),
                'type' => $type,
                'status' => $status,
                'total_amount' => $totalAmount,
                'date' => $data['date'],
                'notes' => $data['notes'] ?? null,
                'created_by' => $userId,
                // Data khusus
                'supplier_id' => $data['supplier_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                // Auto-Approval
                'approved_by' => $status === 'Completed' ? $userId : null, 
                'approved_at' => $status === 'Completed' ? now() : null,
            ]);

            // Tambahkan Items
            foreach ($data['items'] as $item) {
                $transaction->items()->create($item);
            }

            // Jika status Completed, segera proses stok melalui StockMovementService
            if ($status === 'Completed') {
                $this->stockMovementService->processTransactionItems($transaction->refresh(), $movementType);
            }

            DB::commit();
            return $transaction;

        } catch (\Exception $e) {
            DB::rollBack();
            // Logging harus di sini, tapi throw kembali untuk ditangkap di Controller
            throw new \Exception("Gagal membuat transaksi: " . $e->getMessage());
        }
    }

    public function updateTransaction(Transaction $transaction, array $data): Transaction
    {

        $totalAmount = $this->calculateTotalAmount($data['items']);
        $newStatus = $this->determineStatus($totalAmount);
        $movementType = strtolower($transaction->type);
        $userId = auth()->id();

        DB::beginTransaction();
        try {
            // Validasi Stok untuk Outgoing yang Completed
            if ($transaction->type === 'Outgoing' && $newStatus === 'Completed') {
                $this->validateOutgoingStock($data['items']);
            }
            
            // 1. Update Header Data (termasuk reset status persetujuan)
            $updateData = [
                'date' => $data['date'],
                'notes' => $data['notes'] ?? null,
                'supplier_id' => $data['supplier_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'total_amount' => $totalAmount,
                'status' => $newStatus,
                'approved_by' => null, 
                'approved_at' => null,
                'rejected_by' => null,
                'rejected_at' => null,
            ];
            
            $transaction->update($updateData);

            // 2. Update Items: Hapus dan Recreate (karena ini masih tahap draft/pengajuan ulang)
            $transaction->items()->delete(); 
            foreach ($data['items'] as $itemData) {
                TransactionItem::create(array_merge($itemData, ['transaction_id' => $transaction->id]));
            }
            
            // 3. Auto-Approve dan Proses Stok jika statusnya menjadi Completed
            if ($newStatus === 'Completed') {
                $transaction->update([
                    'approved_by' => $userId,
                    'approved_at' => now(),
                ]);
                $this->stockMovementService->processTransactionItems($transaction->refresh(), $movementType);
            }

            DB::commit();
            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Gagal memperbarui transaksi: " . $e->getMessage());
        }
    }

    private function validateOutgoingStock(array $items): void
    {
        $productQuantities = [];
        foreach ($items as $item) {
            $productQuantities[$item['product_id']] = ($productQuantities[$item['product_id']] ?? 0) + $item['quantity'];
        }

        foreach ($productQuantities as $productId => $quantity) {
            $product = Product::find($productId);
            if (!$product || $product->stock < $quantity) {
                throw new \Exception("Stok produk '{$product->name}' tidak cukup ({$product->stock} tersedia, diminta {$quantity}).");
            }
        }
    }

    public function getApprovedSuppliers()
    {
        // Asumsi role sudah diimplementasikan (misal pakai Spatie atau custom)
        // Jika tidak pakai Spatie, ganti dengan: User::where('role', 'Supplier')...
        return User::role('Supplier')->where('is_approved', true)->get();
    }
}