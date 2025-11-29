<?php

namespace App\Services;

use App\Models\RestockOrder;
use App\Models\RestockItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Product; // Pastikan Product diimpor jika nanti diperlukan di sini

class RestockService
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        // Inject TransactionService untuk memicu pembaruan stok
        $this->transactionService = $transactionService;
    }

    /**
     * Membuat Restock Order (PO) baru oleh Manager.
     * * @param array $data Data dari request (termasuk items).
     * @param int $managerId ID Manager yang membuat PO.
     * @param string $poNumber Nomor PO yang sudah dibuat.
     * @return RestockOrder
     */
    public function createOrder(array $data, int $managerId, string $poNumber): RestockOrder
    {
        DB::beginTransaction();
        try {
            // 1. Buat Header Restock Order
            // Pastikan Anda mempassing 'order_date' dari controller jika diperlukan, 
            // jika tidak, gunakan now()
            $order = RestockOrder::create([
                'po_number' => $poNumber,
                'supplier_id' => $data['supplier_id'],
                'created_by' => $managerId,
                'order_date' => now(), // Menggunakan waktu saat ini
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'status' => 'Pending',
                'notes' => $data['notes'] ?? null,
            ]);

            // 2. Simpan Item Restock
            foreach ($data['items'] as $item) {
                // KRITIS: Tambahkan 'unit_price' ke item PO
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'], // ✅ FIX: Simpan harga beli
                ]);
            }

            DB::commit();
            return $order;

        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Gagal membuat Restock Order: " . $e->getMessage());
        }
    }

    /**
     * Memproses penerimaan Restock Order (Status: Received) dan memperbarui stok.
     * * Logika KRITIS: Memanggil TransactionService untuk menjaga konsistensi stok.
     * * @param RestockOrder $order Model RestockOrder.
     * @param int $receiverId ID user (Manager/Staff) yang menerima.
     * @return bool
     */
    public function processReceiving(RestockOrder $order, int $receiverId): bool
    {
        if ($order->status === 'Received') {
            throw new \Exception("Order sudah diterima sebelumnya.");
        }

        DB::beginTransaction();
        try {
            // Buat transaksi masuk + update stok
            $transaction = $this->transactionService
                ->createIncomingTransactionFromRestock($order, $receiverId);

            // Update status + penerima
            $order->update([
                'status' => 'Received',
                'received_at' => now(),
                'received_by' => $receiverId,
            ]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Penerimaan barang gagal: " . $e->getMessage());
        }
    }
}