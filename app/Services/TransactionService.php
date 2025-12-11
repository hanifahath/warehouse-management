<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\User;
use App\Models\RestockOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionService
{
    private $stockMovementService;

    public function __construct(StockMovementService $stockMovementService)
    {
        $this->stockMovementService = $stockMovementService;
    }

    public function createTransaction(array $data, string $type, User $user): Transaction
    {
        DB::beginTransaction();

        try {
            $transactionData = [
                'transaction_number' => $this->generateTransactionNumber($type),
                'type' => strtolower($type),
                'status' => 'pending',
                'total_amount' => $this->calculateTotal($data['items']),
                'date' => $data['date'] ?? now()->toDateString(),
                'notes' => $data['notes'] ?? null,
                'created_by' => $user->id,
                'restock_order_id' => $data['restock_order_id'] ?? null,
            ];
            
            if ($type === 'incoming') {
                $transactionData['supplier_id'] = $data['supplier_id'] ?? null;
                
                if (!empty($data['restock_order_id']) && empty($transactionData['supplier_id'])) {
                    $restockOrder = RestockOrder::find($data['restock_order_id']);
                    if ($restockOrder) {
                        $transactionData['supplier_id'] = $restockOrder->supplier_id;
                    }
                }
                
                $transactionData['restock_status'] = !empty($data['restock_order_id']) 
                    ? 'pending_receipt' 
                    : 'not_restock';
            } else {
                $transactionData['customer_name'] = $data['customer_name'] ?? null;
                $transactionData['restock_status'] = 'not_restock';
                
                foreach ($data['items'] as $item) {
                    $product = Product::find($item['product_id']);
                    if ($product && $product->current_stock < $item['quantity']) {
                        throw new \Exception(
                            "Insufficient stock for {$product->name}. " .
                            "Available: {$product->current_stock}"
                        );
                    }
                }
            }
            
            $transaction = Transaction::create($transactionData);
            
            $this->createTransactionItems($transaction, $data['items']);
            
            DB::commit();
            return $transaction;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateTransaction(Transaction $transaction, array $data): Transaction
    {
        if (!$transaction->isPending()) {
            throw new \Exception('Transaksi hanya bisa diubah ketika status pending.');
        }
        
        DB::beginTransaction();

        try {
            $transaction->update([
                'date' => $data['date'],
                'notes' => $data['notes'] ?? null,
                'supplier_id' => $transaction->isIncoming() ? ($data['supplier_id'] ?? null) : null,
                'customer_name' => $transaction->isOutgoing() ? ($data['customer_name'] ?? null) : null,
            ]);

            if (isset($data['items'])) {
                $transaction->items()->delete();
                $this->createTransactionItems($transaction, $data['items']);
                
                $total = $this->calculateTotal($data['items']);
                $transaction->update(['total_amount' => $total]);
            }

            DB::commit();
            return $transaction->fresh()->load('items.product');
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteTransaction(Transaction $transaction): bool
    {
        if (!$transaction->isPending()) {
            throw new \Exception('Transaksi hanya bisa dihapus ketika status pending.');
        }
        
        DB::beginTransaction();
        
        try {
            $transaction->items()->delete();
            $result = $transaction->delete();
            DB::commit();
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function approveTransaction(Transaction $transaction, User $approver, ?string $notes = null): Transaction
    {
        if (!$transaction->isPending()) {
            throw new \Exception('Transaksi hanya bisa disetujui ketika status pending.');
        }
        
        DB::beginTransaction();
        
        try {
            $newStatus = $transaction->isIncoming() ? 'Verified' : 'Approved';
            
            $transaction->update([
                'status' => $newStatus,
                'approved_by' => $approver->id,
                'approved_at' => now(),
                'notes' => $notes ? ($transaction->notes . "\n\nDisetujui: " . $notes) : $transaction->notes,
            ]);
            
            StockMovementService::updateFromTransaction($transaction);
            
            DB::commit();
            
            return $transaction->fresh();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function rejectTransaction(Transaction $transaction, User $rejecter, string $reason): Transaction
    {
        if (!$transaction->isPending()) {
            throw new \Exception('Transaksi hanya bisa ditolak ketika status pending.');
        }
        
        DB::beginTransaction();
        
        try {
            $transaction->updateStatus('rejected', [
                'rejected_by' => $rejecter->id,
                'rejected_at' => now(),
                'rejection_reason' => $reason,
                'notes' => ($transaction->notes ?? '') . "\n\nDitolak: " . $reason,
            ]);
            
            DB::commit();
            return $transaction->fresh();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function markAsShipped(Transaction $transaction, User $user): Transaction
    {
        if (!$transaction->isOutgoing()) {
            throw new \Exception('Hanya transaksi keluar yang bisa ditandai sebagai shipped.');
        }
        
        if (!$transaction->isApproved()) {
            throw new \Exception('Hanya transaksi dengan status approved yang bisa ditandai sebagai shipped.');
        }
        
        $transaction->updateStatus('shipped');
        return $transaction->fresh();
    }

    public function markAsCompleted(Transaction $transaction, User $user): Transaction
    {
        if (!$transaction->isIncoming()) {
            throw new \Exception('Hanya transaksi masuk yang bisa ditandai sebagai completed.');
        }
        
        if (!$transaction->isVerified()) {
            throw new \Exception('Hanya transaksi dengan status verified yang bisa ditandai sebagai completed.');
        }
        
        $transaction->updateStatus('completed');
        return $transaction->fresh();
    }

    public function getPendingApprovals(User $user, $urgent = false, $perPage = 10)
    {
        if (!$user->isManager() && !$user->isAdmin()) {
            return collect();
        }
        
        $query = Transaction::with(['creator', 'items.product', 'supplier'])
            ->where('status', 'Pending');
        
        if ($urgent) {
            $query->where('is_urgent', true);
        }
        
        return $query->latest()->paginate($perPage);
    }

    public function getStaffHistory(User $user, Request $request)
    {
        if (!$user->isStaff()) {
            return collect();
        }
        
        $query = Transaction::with(['items.product', 'approver'])
            ->where('created_by', $user->id);
        
        $this->applyCommonFilters($query, $request);
        
        return $query->latest()->paginate($request->per_page ?? 20);
    }

    public function getFilteredTransactions(Request $request, User $user)
    {
        $query = Transaction::with(['items.product', 'creator', 'supplier', 'approver']);
        
        $this->applyRoleFilter($query, $user);
        $this->applyCommonFilters($query, $request);
        
        return $query->latest()->paginate($request->per_page ?? 20);
    }

    private function createTransactionItems(Transaction $transaction, array $items): void
    {
        foreach ($items as $item) {
            $transaction->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price_at_transaction' => $item['price_at_transaction'],
            ]);
        }
    }

    private function generateTransactionNumber(string $type): string
    {
        $prefix = $type === 'incoming' ? 'IN' : 'OUT';
        $date = date('Ymd');
        $random = strtoupper(Str::random(6));
        
        return "{$prefix}-{$date}-{$random}";
    }

    private function calculateTotal(array $items): float
    {
        return collect($items)->sum(fn($item) => 
            ($item['quantity'] ?? 0) * ($item['price_at_transaction'] ?? 0)
        );
    }

    private function applyRoleFilter($query, User $user): void
    {
        switch ($user->role) {
            case 'staff':
                $query->where('created_by', $user->id);
                break;
            case 'supplier':
                $query->where('type', 'incoming')->where('supplier_id', $user->id);
                break;
            case 'manager':
            case 'admin':
                break;
            default:
                $query->where('created_by', $user->id);
                break;
        }
    }

    private function applyCommonFilters($query, Request $request): void
    {
        if ($request->filled('type')) {
            $query->where('type', strtolower($request->type));
        }
        
        if ($request->filled('status')) {
            $query->where('status', ucfirst(strtolower($request->status)));
        }
        
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }
        
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }
        
        if ($request->filled('supplier_id') && in_array(auth()->user()->role, ['manager', 'admin'])) {
            $query->where('supplier_id', $request->supplier_id);
        }
        
        if ($request->filled('created_by') && in_array(auth()->user()->role, ['manager', 'admin'])) {
            $query->where('created_by', $request->created_by);
        }
        
        if ($request->filled('search')) {
            $this->applySearchFilter($query, $request->search);
        }
    }

    private function applySearchFilter($query, string $search): void
    {
        $query->where(function($q) use ($search) {
            $q->where('transaction_number', 'like', "%{$search}%")
              ->orWhere('customer_name', 'like', "%{$search}%")
              ->orWhere('notes', 'like', "%{$search}%")
              ->orWhereHas('creator', function($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
              })
              ->orWhereHas('supplier', function($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
              })
              ->orWhereHas('items.product', function($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
              });
        });
    }

    public function getAvailableProducts()
    {
        return Product::orderBy('name')
            ->get(['id', 'sku', 'name', 'current_stock', 'min_stock', 'purchase_price', 'selling_price']);
    }

    public function getApprovedSuppliers()
    {
        return User::where('role', 'supplier')
            ->where('is_approved', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone']);
    }
}