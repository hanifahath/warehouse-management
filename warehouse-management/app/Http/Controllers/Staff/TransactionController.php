<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionStoreRequest;
use App\Http\Requests\TransactionUpdateRequest;
use App\Models\Transaction;
use App\Models\Product;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class TransactionController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;

        $this->middleware('auth');
        $this->middleware('can:viewAny,' . Transaction::class)->only('index');
        $this->middleware('can:create,' . Transaction::class)->only(['createIncoming', 'storeIncoming', 'createOutgoing', 'storeOutgoing']);
    }

    /** Daftar transaksi */
    public function index(Request $request): View
    {
        $filterType = $request->query('type');
        $query = Transaction::query();
        $title = 'Daftar Semua Transaksi';

        if (auth()->user()->role === 'Staff') {
            $query->where('created_by', auth()->id());
            $title = 'Daftar Transaksi Saya';
        }

        if ($filterType) {
            $query->where('type', $filterType);
            $title = "Daftar Transaksi {$filterType}" . (auth()->user()->role === 'Staff' ? ' (Saya)' : '');
        }

        $transactions = $query->latest()->paginate(10);

        return view('transactions.index', compact('transactions', 'filterType', 'title'));
    }

    /** Lihat detail transaksi */
    public function show(Transaction $transaction): View
    {
        Gate::authorize('view', $transaction);
        $transaction->load(['items.product', 'creator', 'approver']);

        return view('transactions.show', compact('transaction'));
    }

    /** Form buat transaksi masuk */
    public function createIncoming(): View
    {
        $suppliers = $this->transactionService->getApprovedSuppliers();
        $products = Product::all();

        return view('transactions.create_incoming', compact('suppliers', 'products'));
    }

    /** Simpan transaksi masuk */
    public function storeIncoming(TransactionStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $transaction = $this->transactionService->createTransaction($data, 'Incoming');

            $message = $transaction->status === 'Completed'
                ? 'Transaksi Masuk berhasil dibuat dan diselesaikan (stok diperbarui).'
                : 'Transaksi Masuk berhasil dibuat, menunggu persetujuan Manager.';

            return redirect()->route('staff.transactions.index')->with('success', $message);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /** Form buat transaksi keluar */
    public function createOutgoing(): View
    {
        $products = Product::all();
        return view('transactions.create_outgoing', compact('products'));
    }

    /** Simpan transaksi keluar */
    public function storeOutgoing(TransactionStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $transaction = $this->transactionService->createTransaction($data, 'Outgoing');

            $message = $transaction->status === 'Completed'
                ? 'Transaksi Keluar berhasil dibuat dan diselesaikan (stok diperbarui).'
                : 'Transaksi Keluar berhasil dibuat, menunggu persetujuan Manager.';

            return redirect()->route('staff.transactions.show', $transaction->id)->with('success', $message);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /** Form edit transaksi */
    public function edit(Transaction $transaction): View
    {
        Gate::authorize('update', $transaction);

        $transaction->load('items.product');
        $products = Product::all();
        $suppliers = $this->transactionService->getApprovedSuppliers();

        return view('transactions.edit', compact('transaction', 'products', 'suppliers'));
    }

    /** Update transaksi */
    public function update(TransactionUpdateRequest $request, Transaction $transaction): RedirectResponse
    {
        $data = $request->validated();
        Gate::authorize('update', $transaction);

        try {
            $transaction = $this->transactionService->updateTransaction($transaction, $data);

            $message = $transaction->status === 'Completed'
                ? 'Transaksi berhasil diperbarui dan otomatis diselesaikan.'
                : 'Transaksi berhasil diperbarui dan diajukan ulang untuk persetujuan.';

            return redirect()->route('staff.transactions.show', $transaction->id)->with('success', $message);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
