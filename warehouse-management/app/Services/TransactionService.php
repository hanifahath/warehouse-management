<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Models\RestockOrder; // Impor RestockOrder agar tipe data jelas

class TransactionService
{
    /**
     * Memproses persetujuan transaksi dan memperbarui stok produk.
     * @param Transaction $transaction
     * @param int $managerId ID user Manager yang menyetujui.
     * @return bool
     */
    public function processApproval(Transaction $transaction, int $managerId): bool
    {
        // 1. Cek status: hanya transaksi Pending yang bisa diproses
        if ($transaction->status !== 'Pending') {
            // Sebaiknya throw exception agar controller bisa menangkap dan menampilkan pesan spesifik
            throw new \Exception("Transaksi tidak dalam status Pending.");
        }

        // 2. Mulai Database Transaction (CRITICAL!)
        DB::beginTransaction();

        try {
            // 3. Update Header Transaksi
            $transaction->update([
                'status' => 'Approved', 
                'approved_by' => $managerId,
            ]);

            // 4. Proses Item dan Update Stok
            foreach ($transaction->items as $item) { // Pastikan relasi items() ada di Model Transaction
                // Muat ulang produk untuk memastikan kita bekerja dengan data stok terbaru
                $product = $item->product->fresh(); 
                
                // Tentukan perubahan kuantitas
                $quantityChange = ($transaction->type === 'Incoming') 
                                ? $item->quantity 
                                : -$item->quantity;
                
                // Pengecekan Stok Ulang (Hanya untuk Outgoing)
                if ($transaction->type === 'Outgoing' && ($product->stock + $quantityChange) < 0) {
                    throw new \Exception("Stok produk '{$product->name}' tidak mencukupi untuk persetujuan.");
                }

                // Update stok di tabel products
                $product->increment('stock', $quantityChange); 
            }

            // 5. Commit Transaction
            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            // Lemparkan kembali exception yang lebih spesifik agar Controller bisa menampilkannya
            throw new \Exception("Persetujuan transaksi gagal: " . $e->getMessage());
        }
    }

    /**
     * Membuat dan Menyetujui Transaksi Masuk berdasarkan Restock Order.
     * Dipanggil saat Restock Order diterima (Received).
     * @param RestockOrder $restockOrder
     * @param int $receiverId ID user (Manager/Staff) yang menerima.
     * @return Transaction
     */
    public function createIncomingTransactionFromRestock(RestockOrder $restockOrder, int $receiverId): Transaction
    {
        DB::beginTransaction();
        try {
            // 1. Buat Header Transaksi
            $transaction = Transaction::create([
                'transaction_number' => 'IN-PO-' . $restockOrder->po_number,
                'type' => 'Incoming',
                'supplier_id' => $restockOrder->supplier_id,
                'created_by' => $receiverId, // Yang menerima = yang membuat Transaksi di sini
                'approved_by' => $receiverId, // Dianggap disetujui instan karena Manager/Staff yang menerima
                'status' => 'Approved', // Langsung Approved karena sudah melewati tahap PO
                'date' => now(),
                'notes' => 'Penerimaan barang dari PO: ' . $restockOrder->po_number,
            ]);

            // 2. Proses Item dan Update Stok
            foreach ($restockOrder->items as $item) {
                // Simpan Item Transaksi
                $transaction->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    // ✅ FIX KRITIS: Gunakan harga beli yang dicatat di RestockItem (unit_price)
                    'price_at_transaction' => $item->unit_price, 
                ]);
                
                // Update stok di tabel products
                // Muat ulang produk untuk memastikan kita bekerja dengan data stok terbaru
                $product = $item->product->fresh(); 
                $product->increment('stock', $item->quantity); 
            }

            DB::commit();
            return $transaction;

        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Gagal membuat Transaksi Masuk dari Restock Order: " . $e->getMessage());
        }
    }
    
    // Asumsi: Di sini Anda akan menambahkan metode lain seperti createIncomingTransaction()
    // dan createOutgoingTransaction() yang dipanggil dari TransactionController (transaksi manual).
}