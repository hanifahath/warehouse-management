<?php

namespace App\Services;

use App\Models\RestockOrder;
use App\Models\RestockItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RestockService
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService = null)
    {
        $this->transactionService = $transactionService;
    }

    public function createRestockOrder(array $data): RestockOrder
    {
        return DB::transaction(function () use ($data) {
            $latest = RestockOrder::latest()->first();
            $nextId = $latest ? $latest->id + 1 : 1;
            $poNumber = 'PO-' . str_pad($nextId, 6, '0', STR_PAD_LEFT) . '-' . date('Ymd');
            
            $restock = RestockOrder::create([
                'po_number' => $poNumber,
                'supplier_id' => $data['supplier_id'],
                'order_date' => $data['order_date'] ?? now(),
                'expected_delivery_date' => $data['expected_delivery_date'],
                'notes' => $data['notes'] ?? null,
                'status' => 'Pending',
                'manager_id' => Auth::id(),
                'total_amount' => $data['total_amount'] ?? 0,
            ]);
            
            foreach ($data['items'] as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    throw new \Exception("Product not found: {$item['product_id']}");
                }
                
                $unitPrice = $product->purchase_price ?? 0;
                $subtotal = $unitPrice * $item['quantity'];
                
                RestockItem::create([
                    'restock_order_id' => $restock->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $product->name,
                    'unit_price' => $unitPrice,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                ]);
            }
            
            return $restock->fresh()->load([
                'items.product', 
                'supplier', 
                'manager'
            ]);
        });
    }

    public function updateRestockOrder(RestockOrder $restock, array $data): RestockOrder
    {
        if ($restock->status !== 'Pending') {
            throw new \Exception('Restock order can only be updated when status is Pending.');
        }
        
        return DB::transaction(function () use ($restock, $data) {
            $restock->update([
                'supplier_id' => $data['supplier_id'],
                'expected_delivery_date' => $data['expected_delivery_date'],
                'notes' => $data['notes'] ?? null,
                'updated_by' => Auth::id(),
            ]);
            
            $restock->items()->delete();
            
            $totalAmount = 0;
            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $unitPrice = $product->purchase_price ?? 0;
                $subtotal = $unitPrice * $item['quantity'];
                $totalAmount += $subtotal;
                
                RestockItem::create([
                    'restock_order_id' => $restock->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $product->name,
                    'unit_price' => $unitPrice,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                ]);
            }
            
            $restock->update(['total_amount' => $totalAmount]);
            
            return $restock->fresh()->load(['items.product', 'supplier', 'creator']);
        });
    }

    public function confirmBySupplier(RestockOrder $restock): RestockOrder
    {
        if (Auth::user()->isSupplier() && $restock->supplier_id !== Auth::id()) {
            throw new \Exception('Order ini bukan milik Anda.');
        }
        
        if ($restock->status !== 'Pending') {
            throw new \Exception('Order hanya bisa dikonfirmasi ketika status Pending.');
        }
        
        return DB::transaction(function () use ($restock) {
            $restock->update([
                'status' => 'Confirmed',
                'confirmed_at' => now(),
                'notes' => $restock->notes . "\n\nDikonfirmasi oleh supplier pada " . now()->format('d/m/Y H:i'),
            ]);
            
            return $restock->fresh();
        });
    }

    public function markInTransit(RestockOrder $restock): RestockOrder
    {
        if ($restock->status !== 'Confirmed') {
            throw new \Exception('Order hanya bisa dikirim ketika status Confirmed.');
        }
        
        return DB::transaction(function () use ($restock) {
            $restock->update([
                'status' => 'In Transit',
                'in_transit_at' => now(),
                'notes' => $restock->notes . "\n\nDikirim oleh supplier pada " . now()->format('d/m/Y H:i'),
            ]);
            
            return $restock->fresh();
        });
    }

    public function receiveOrder(RestockOrder $restock): RestockOrder
    {
        if ($restock->status !== 'In Transit') {
            throw new \Exception('Order hanya bisa diterima ketika status In Transit.');
        }
        
        return DB::transaction(function () use ($restock) {
            $restock->update([
                'status' => 'Received',
                'received_at' => now(),
                'received_by' => Auth::id(),
                'notes' => $restock->notes . "\n\nDiterima oleh gudang pada " . now()->format('d/m/Y H:i'),
            ]);
            
            StockMovementService::updateFromRestock($restock);

            if ($this->transactionService) {
                $this->createTransactionFromRestock($restock);
            }
            
            return $restock->fresh();
        });
    }

    private function updateProductStock(RestockOrder $restock): void
    {
        foreach ($restock->items as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->increment('current_stock', $item->quantity);
            }
        }
    }

    private function createTransactionFromRestock(RestockOrder $restock): void
    {
        try {
            $transactionData = [
                'type' => 'incoming',
                'supplier_id' => $restock->supplier_id,
                'date' => now(),
                'notes' => "Restock dari PO: {$restock->po_number}",
                'items' => $restock->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price_at_transaction' => $item->unit_price,
                    ];
                })->toArray(),
            ];
            
            $this->transactionService->createTransaction($transactionData);
        } catch (\Exception $e) {
            // Silent fail - transaction creation is optional
        }
    }

    public function cancelOrder(RestockOrder $restock, string $reason): RestockOrder
    {
        if (!in_array($restock->status, ['Pending', 'Confirmed'])) {
            throw new \Exception('Order hanya bisa dibatalkan ketika status Pending atau Confirmed.');
        }
        
        return DB::transaction(function () use ($restock, $reason) {
            $restock->update([
                'status' => 'Cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => Auth::id(),
                'cancellation_reason' => $reason,
                'notes' => $restock->notes . "\n\nDibatalkan pada " . now()->format('d/m/Y H:i') . 
                           "\nAlasan: " . $reason,
            ]);
            
            return $restock->fresh();
        });
    }

    public function getFilteredOrders(Request $request)
    {
        $query = RestockOrder::with(['supplier', 'items.product', 'creator', 'receiver'])
            ->orderBy('order_date', 'desc');
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('po_number', 'like', '%' . $search . '%')
                ->orWhereHas('supplier', function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                })
                ->orWhereHas('items.product', function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('sku', 'like', '%' . $search . '%');
                });
            });
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }
        
        if (Auth::user()->isSupplier()) {
            $query->where('supplier_id', Auth::id());
        }
        
        if (Auth::user()->isManager()) {
            $query->where(function($q) {
                $q->where('manager_id', Auth::id())
                  ->orWhere('status', '!=', 'Pending');
            });
        }
        
        return $query->paginate($request->per_page ?? 20);
    }

    public function getSupplierOrders(Request $request)
    {
        $query = RestockOrder::with(['manager', 'items.product'])
            ->where('supplier_id', auth()->id());
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('po_number', 'like', '%' . $search . '%')
                ->orWhereHas('manager', function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('items.product', function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('sku', 'like', '%' . $search . '%');
                });
            });
        }
        
        if ($request->filled('date_from')) {
            $query->where('order_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('order_date', '<=', $request->date_to);
        }
        
        return $query->latest()->paginate(20);
    }

    public function getSupplierStats()
    {
        $supplierId = auth()->id();
        
        return [
            'totalOrders' => RestockOrder::where('supplier_id', $supplierId)->count(),
            'pendingCount' => RestockOrder::where('supplier_id', $supplierId)
                ->where('status', 'Pending')->count(),
            'confirmedCount' => RestockOrder::where('supplier_id', $supplierId)
                ->where('status', 'Confirmed')->count(),
            'inTransitCount' => RestockOrder::where('supplier_id', $supplierId)
                ->where('status', 'In Transit')->count(),
            'receivedCount' => RestockOrder::where('supplier_id', $supplierId)
                ->where('status', 'Received')->count(),
            'cancelledCount' => RestockOrder::where('supplier_id', $supplierId)
                ->where('status', 'Cancelled')->count(),
        ];
    }
}