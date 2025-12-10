<?php

namespace App\Services;

use App\Models\RestockOrder;
use App\Models\RestockItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RestockService
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService = null)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Create a new restock order
     */
    public function createRestockOrder(array $data): RestockOrder
    {
        Log::info('Creating restock order with data:', $data);
        
        return DB::transaction(function () use ($data) {
            // Generate PO number
            $latest = RestockOrder::latest()->first();
            $nextId = $latest ? $latest->id + 1 : 1;
            $poNumber = 'PO-' . str_pad($nextId, 6, '0', STR_PAD_LEFT) . '-' . date('Ymd');
            
            // Create restock order
            $restock = RestockOrder::create([
                'po_number' => $poNumber,
                'supplier_id' => $data['supplier_id'],
                'order_date' => $data['order_date'] ?? now(),
                'expected_delivery_date' => $data['expected_delivery_date'],
                'notes' => $data['notes'] ?? null,
                'status' => 'Pending',
                'manager_id' => Auth::id(), // Ini adalah creator
                'total_amount' => $data['total_amount'] ?? 0,
            ]);
            
            Log::info('Restock order created:', ['id' => $restock->id, 'po_number' => $poNumber]);
            
            // Add items
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
            
            // PERBAIKAN: Gunakan relationship yang benar
            return $restock->fresh()->load([
                'items.product', 
                'supplier', 
                'manager' // Ganti 'creator' dengan 'manager'
            ]);
        });
    }

    /**
     * Update restock order
     */
    public function updateRestockOrder(RestockOrder $restock, array $data): RestockOrder
    {
        if ($restock->status !== 'Pending') {
            throw new \Exception('Restock order can only be updated when status is Pending.');
        }
        
        return DB::transaction(function () use ($restock, $data) {
            // Update basic info
            $restock->update([
                'supplier_id' => $data['supplier_id'],
                'expected_delivery_date' => $data['expected_delivery_date'],
                'notes' => $data['notes'] ?? null,
                'updated_by' => Auth::id(),
            ]);
            
            // Delete old items
            $restock->items()->delete();
            
            // Add new items
            $totalAmount = 0;
            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $unitPrice = $this->getProductPurchasePrice($product);
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
            
            // Update total amount
            $restock->update(['total_amount' => $totalAmount]);
            
            return $restock->fresh()->load(['items.product', 'supplier', 'creator']);
        });
    }

    /**
     * Get product purchase price
     */
    private function getProductPurchasePrice(Product $product): float
    {
        return $product->purchase_price ?? 0;
    }

    /**
     * Confirm order by supplier
     */
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

    /**
     * Mark as in transit
     */
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

    /**
     * Receive order
     */
    public function receiveOrder(RestockOrder $restock): RestockOrder
    {
        if ($restock->status !== 'In Transit') {
            throw new \Exception('Order hanya bisa diterima ketika status In Transit.');
        }
        
        return DB::transaction(function () use ($restock) {
            // Update order status
            $restock->update([
                'status' => 'Received',
                'received_at' => now(),
                'received_by' => Auth::id(),
                'notes' => $restock->notes . "\n\nDiterima oleh gudang pada " . now()->format('d/m/Y H:i'),
            ]);
            
            // Update product stock
            //$this->updateProductStock($restock);
            StockMovementService::updateFromRestock($restockOrder);

            // Create transaction record if service exists
            if ($this->transactionService) {
                $this->createTransactionFromRestock($restock);
            }
            
            return $restock->fresh();
        });
    }

    /**
     * Update product stock after receiving
     */
    private function updateProductStock(RestockOrder $restock): void
    {
        foreach ($restock->items as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->increment('current_stock', $item->quantity);
            }
        }
    }

    /**
     * Create transaction from restock
     */
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
            \Log::error('Failed to create transaction for restock: ' . $e->getMessage());
        }
    }

    /**
     * Cancel order
     */
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

    /**
     * Get restock orders with filters
     */
    public function getFilteredOrders(Request $request)
    {
        $query = RestockOrder::with(['supplier', 'items.product', 'creator', 'receiver'])
            ->orderBy('order_date', 'desc');
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        
        // Filter by PO number
        if ($request->filled('po_number')) {
            $query->where('po_number', 'like', '%' . $request->po_number . '%');
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }
        
        // Role-based filtering
        if (Auth::user()->isSupplier()) {
            $query->where('supplier_id', Auth::id());
        }
        
        // Filter by current user's role
        if (Auth::user()->isManager()) {
            $query->where(function($q) {
                $q->where('manager_id', Auth::id())
                  ->orWhere('status', '!=', 'Pending'); // Manager can see all non-pending
            });
        }
        
        return $query->paginate($request->per_page ?? 20);
    }

    /**
     * Get restock statistics
     */
    public function getStats(): array
    {
        $query = RestockOrder::query();
        
        // For supplier, only their orders
        if (Auth::user()->isSupplier()) {
            $query->where('supplier_id', Auth::id());
        }
        
        $total = $query->count();
        
        return [
            'total_orders' => $total,
            'pending_orders' => (clone $query)->where('status', 'Pending')->count(),
            'confirmed_orders' => (clone $query)->where('status', 'Confirmed')->count(),
            'in_transit_orders' => (clone $query)->where('status', 'In Transit')->count(),
            'received_orders' => (clone $query)->where('status', 'Received')->count(),
            'cancelled_orders' => (clone $query)->where('status', 'Cancelled')->count(),
            'total_amount' => (clone $query)->where('status', 'Received')->sum('total_amount'),
        ];
    }

    /**
     * Get monthly statistics for charts
     */
    public function getMonthlyStats(): array
    {
        $query = RestockOrder::select(
            DB::raw('DATE_FORMAT(order_date, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_amount) as amount')
        );
        
        if (Auth::user()->isSupplier()) {
            $query->where('supplier_id', Auth::id());
        }
        
        return $query->where('order_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();
    }

    // RestockService.php
    public function getSupplierOrders(Request $request)
    {
        $query = RestockOrder::with(['manager', 'items.product'])
            ->where('supplier_id', auth()->id());
        
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter berdasarkan PO number
        if ($request->filled('search')) {
            $query->where('po_number', 'like', '%' . $request->search . '%');
        }
        
        // Filter berdasarkan date range
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