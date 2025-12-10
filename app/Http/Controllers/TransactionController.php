<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
Use App\Models\RestockOrder;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use App\Http\Requests\{
    TransactionStoreRequest,
    TransactionUpdateRequest,
    TransactionApproveRequest,
    TransactionRejectRequest
};

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
        $this->middleware('auth');
    }

    // ====================== CRUD ACTIONS ======================
    
    public function index(Request $request)
    {
        $this->authorize('viewAny', Transaction::class);
        
        $transactions = $this->transactionService->getFilteredTransactions(
            $request, 
            auth()->user()
        );
        
        return view('transactions.index', array_merge(
            compact('transactions'),
            $this->getFilterOptions()
        ));
    }

    public function createIncoming(Request $request)
    {
        $this->authorize('create', Transaction::class);
        
        $restockOrders = RestockOrder::readyForReceiving()
            ->orderBy('po_number', 'desc')
            ->get(['id', 'po_number', 'supplier_id']);

        return view('transactions.create_incoming', [
            'products' => $this->transactionService->getAvailableProducts(),
            'suppliers' => $this->transactionService->getApprovedSuppliers(),
            'restockOrders' => $restockOrders,
            'selectedRestockOrder' => $request->has('restock_order_id') 
                ? RestockOrder::find($request->restock_order_id)
                : null,
        ]);
    }

    public function storeIncoming(TransactionStoreRequest $request)
    {
        $this->authorize('create', Transaction::class);
        
        $validated = $request->validated();
        
        if ($request->has('restock_order_id') && !empty($request->restock_order_id)) {
            $validated['restock_order_id'] = $request->restock_order_id;
            
            $restockOrder = RestockOrder::findOrFail($validated['restock_order_id']);
            
            if (empty($validated['supplier_id'])) {
                $validated['supplier_id'] = $restockOrder->supplier_id;
            }
            
            $validated['restock_status'] = 'pending_receipt';
        } else {
            $validated['restock_status'] = 'not_restock';
        }

        return $this->handleTransactionCreation($request->validated(), 'incoming');
    }

    public function createOutgoing()
    {
        $this->authorize('create', Transaction::class);
        
        return view('transactions.create_outgoing', [
            'products' => $this->transactionService->getAvailableProducts(),
            'restockOrders' => [],
        ]);
    }

    public function storeOutgoing(TransactionStoreRequest $request)
    {
        $this->authorize('create', Transaction::class);

        if ($request->has('restock_order_id') && !empty($request->restock_order_id)) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'restock_order_id' => 'Restock order can only be linked to Incoming transactions'
                ]);
        }
        
        $validated = $request->validated();
        $validated['restock_status'] = 'not_restock';
        
        return $this->handleTransactionCreation($request->validated(), 'outgoing');
    }

    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        $transaction->load(['items.product', 'creator', 'supplier', 'approver']);
        
        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        
        return view('transactions.edit', [
            'transaction' => $transaction,
            'products' => $this->transactionService->getAvailableProducts(),
            'suppliers' => $this->transactionService->getApprovedSuppliers()
        ]);
    }

    public function update(TransactionUpdateRequest $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        
        try {
            $transaction = $this->transactionService->updateTransaction(
                $transaction, 
                $request->validated()
            );
            
            return redirect()->route('transactions.show', $transaction)
                ->with('success', 'Transaksi berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);
        
        try {
            $this->transactionService->deleteTransaction($transaction);
            
            return redirect()->route('transactions.index')
                ->with('success', 'Transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // ====================== APPROVAL ACTIONS ======================
    
    public function approve(TransactionApproveRequest $request, Transaction $transaction)
    {
        $this->authorize('approve', $transaction);
        
        try {
            $transaction = $this->transactionService->approveTransaction(
                $transaction,
                auth()->user(),
                $request->notes
            );
            
            return redirect()->route('transactions.show', $transaction)
                ->with('success', 'Transaksi berhasil disetujui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function reject(TransactionRejectRequest $request, Transaction $transaction)
    {
        $this->authorize('reject', $transaction);
        
        try {
            $transaction = $this->transactionService->rejectTransaction(
                $transaction,
                auth()->user(),
                $request->rejection_reason
            );
            
            return redirect()->route('transactions.show', $transaction)
                ->with('success', 'Transaksi berhasil ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // ====================== STATUS ACTIONS ======================
    
    public function ship(Request $request, Transaction $transaction)
    {
        $this->authorize('updateStock', $transaction);
        
        try {
            $transaction = $this->transactionService->markAsShipped($transaction, auth()->user());
            
            return redirect()->route('transactions.show', $transaction)
                ->with('success', 'Transaksi berhasil ditandai sebagai shipped.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function complete(Request $request, Transaction $transaction)
    {
        $this->authorize('updateStock', $transaction);
        
        try {
            $transaction = $this->transactionService->markAsCompleted($transaction, auth()->user());
            
            return redirect()->route('transactions.show', $transaction)
                ->with('success', 'Transaksi berhasil ditandai sebagai completed.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // ====================== VIEW ACTIONS ======================
    
    public function pendingApprovals(Request $request)
    {
        $this->authorize('viewPendingApprovals', Transaction::class);
    
        $urgent = $request->has('urgent') && $request->boolean('urgent');
        $perPage = $request->input('per_page', 10);
        
        $transactions = $this->transactionService->getPendingApprovals(
            auth()->user(), 
            $urgent, 
            $perPage
        );
        
        return view('transactions.pending-approvals', compact('transactions'));
    }

    public function history(Request $request)
    {
        $this->authorize('viewHistory', Transaction::class);
        
        $transactions = $this->transactionService->getStaffHistory(
            auth()->user(), 
            $request
        );
        
        return view('transactions.history', compact('transactions'));
    }

    // ====================== HELPER METHODS ======================
    
    private function handleTransactionCreation(array $data, string $type)
    {
        try {
            // Delegate ke service
            $transaction = $this->transactionService->createTransaction($data, $type, auth()->user());
            
            return redirect()->route('transactions.show', $transaction)
                ->with('success', ucfirst($type) . ' transaction created successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create transaction: ' . $e->getMessage()]);
        }
    }

    private function getFilterOptions(): array
    {
        return [
            'statusOptions' => [
                'pending' => 'Pending',
                'approved' => 'Approved',
                'verified' => 'Verified',
                'rejected' => 'Rejected',
                'completed' => 'Completed',
                'shipped' => 'Shipped',
            ],
            'typeOptions' => [
                'incoming' => 'Incoming',
                'outgoing' => 'Outgoing',
            ],
            'suppliers' => User::where('role', 'supplier')
                ->where('is_approved', true)
                ->orderBy('name')
                ->get(),
            'staff' => User::where('role', 'staff')
                ->orderBy('name')
                ->get(),
        ];
    }

    public function getRestockOrderItems(Request $request, $id)
    {
        $this->authorize('create', Transaction::class);
        
        try {
            $restockOrder = RestockOrder::with(['items.product'])
                ->where('status', 'received')
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'supplier_id' => $restockOrder->supplier_id,
                    'items' => $restockOrder->items->map(function ($item) {
                        return [
                            'product_id' => $item->product_id,
                            'product_name' => $item->product->name,
                            'sku' => $item->product->sku,
                            'ordered_quantity' => $item->quantity,
                            'unit' => $item->product->unit,
                            'current_stock' => $item->product->current_stock,
                            'price' => $item->product->purchase_price,
                        ];
                    }),
                    'po_number' => $restockOrder->po_number,
                    'order_date' => $restockOrder->order_date->format('Y-m-d'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Restock order not found or not ready for receiving'
            ], 404);
        }
    }
}