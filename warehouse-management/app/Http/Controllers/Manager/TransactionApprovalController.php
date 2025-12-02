<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\ManagerTransactionService;
use Illuminate\Http\Request;

class TransactionApprovalController extends Controller
{
    private ManagerTransactionService $transactionService;

    public function __construct(ManagerTransactionService $transactionService)
    {
        $this->middleware('auth');
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Transaction::class);

        $query = Transaction::query()
            ->where('status', 'Pending Approval')
            ->with(['creator', 'items.product']);

        if ($q = $request->get('q')) {
            $query->where(function($sub) use ($q) {
                $sub->where('transaction_number', 'like', "%{$q}%")
                    ->orWhere('customer_name', 'like', "%{$q}%");
            });
        }

        $transactions = $query->latest()->paginate(20)->withQueryString();

        return view('manager.transaction_approvals.index', [
            'transactions' => $transactions,
        ]);
    }

    public function approve(Transaction $transaction)
    {
        $this->authorize('approve', $transaction);

        try {
            $this->transactionService->approveTransaction($transaction, auth()->id());
            return back()->with('success', 'Transaksi berhasil disetujui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Transaction $transaction)
    {
        $this->authorize('reject', $transaction);

        try {
            $this->transactionService->rejectTransaction($transaction, auth()->id());
            return back()->with('success', 'Transaksi berhasil ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
