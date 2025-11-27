<?php

namespace App\Services;

use App\Models\RestockOrder;
use App\Models\RestockItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            $order = RestockOrder::create([
                'po_number' => $poNumber,
                'supplier_id' => $data['supplier_id'],
                'created_by' => $managerId,
                'order_date' => $data['order_date'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'status' => 'Pending',
                'notes' => $data['notes'] ?? null,
            ]);

            // 2. Simpan Item Restock
            foreach ($data['items'] as $item) {
                // Asumsi items memiliki product_id dan quantity
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
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
        // Pengecekan status terakhir
        if ($order->status === 'Received') {
            throw new \Exception("Order sudah diterima sebelumnya.");
        }
        
        DB::beginTransaction();
        try {
            // 1. Panggil Transaction Service untuk membuat Transaksi Masuk
            //    Metode ini akan mencatat Transaksi dan mengupdate stock produk.
            $this->transactionService->createIncomingTransactionFromRestock($order, $receiverId);
            
            // 2. Update Status Restock Order menjadi Received
            $order->update(['status' => 'Received']);

            DB::commit();
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            // Lemparkan kembali exception yang lebih spesifik agar Controller bisa menampilkannya
            throw new \Exception("Penerimaan barang gagal diproses: " . $e->getMessage());
        }
    }
}