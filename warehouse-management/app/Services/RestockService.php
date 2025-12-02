<?php

namespace App\Services;

use App\Models\RestockOrder;
use App\Models\RestockItem;
use App\Models\Product;
use App\Services\TransactionService;
use Illuminate\Support\Facades\DB;

class RestockService
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function createRestock(array $data): RestockOrder
    {
        DB::beginTransaction();
        try {
            $poNumber = 'PO-' . now()->format('YmdHis');

            $order = RestockOrder::create([
                'po_number' => $poNumber,
                'supplier_id' => $data['supplier_id'],
                'order_date' => $data['order_date'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => 'Pending',
            ]);

            foreach ($data['items'] as $item) {
                RestockItem::create([
                    'restock_order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['unit_price'] * $item['quantity'],
                ]);
            }

            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Gagal membuat PO: " . $e->getMessage());
        }
    }

    public function updateRestock(RestockOrder $order, array $data): RestockOrder
    {
        if ($order->status !== 'Pending') {
            throw new \Exception("PO hanya bisa diupdate jika status Pending.");
        }

        DB::beginTransaction();
        try {
            $order->update([
                'supplier_id' => $data['supplier_id'],
                'order_date' => $data['order_date'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            if (isset($data['items'])) {
                $order->items()->delete();
                foreach ($data['items'] as $item) {
                    RestockItem::create([
                        'restock_order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'unit_price' => $item['unit_price'],
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['unit_price'] * $item['quantity'],
                    ]);
                }
            }

            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Gagal update PO: " . $e->getMessage());
        }
    }

    public function updateStatus(RestockOrder $order, string $status)
    {
        DB::beginTransaction();
        try {
            $order->update(['status' => $status]);

            if ($status === 'Received') {
                // Auto create Incoming Transaction
                $items = $order->items->map(function($item) {
                    return [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price_at_transaction' => $item->unit_price,
                    ];
                })->toArray();

                $transactionData = [
                    'date' => now(),
                    'notes' => "Incoming from PO {$order->po_number}",
                    'supplier_id' => $order->supplier_id,
                    'items' => $items,
                ];

                $this->transactionService->createTransaction($transactionData, 'Incoming');
            }

            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Gagal update status: " . $e->getMessage());
        }
    }
}
