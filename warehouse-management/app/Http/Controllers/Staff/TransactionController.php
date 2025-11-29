<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionStoreRequest;
use App\Http\Requests\TransactionUpdateRequest;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use DB;
use App\Services\InventoryService;

class TransactionController extends Controller
{
    protected $service;

    public function __construct($service) // tetap inject service jika ada
    {
        $this->service = $service;
        $this->middleware(['auth', 'role:Admin|Manager|Staff']);
    }

    public function index(Request $request): View
    {
        $filterType = $request->query('type');
        $query = Transaction::query();
        $baseTitle = 'Daftar Semua Transaksi';

        if (auth()->user()->role === 'Staff') {
            $query->where('created_by', auth()->id());
            $baseTitle = 'Daftar Transaksi Saya';
        }

        if ($filterType) {
            if ($filterType === 'Incoming') {
                $query->where('type', 'Incoming');
                $baseTitle = 'Daftar Transaksi Masuk' . (auth()->user()->role === 'Staff' ? ' (Saya)' : '');
            } elseif ($filterType === 'Outgoing') {
                $query->where('type', 'Outgoing');
                $baseTitle = 'Daftar Transaksi Keluar' . (auth()->user()->role === 'Staff' ? ' (Saya)' : '');
            }
        }

        $transactions = $query->latest()->paginate(10);

        return view('transactions.index', [
            'transactions' => $transactions,
            'currentFilter' => $filterType,
            'title' => $baseTitle,
        ]);
    }

    public function show(Transaction $transaction): View
    {
        $transaction->load(['items.product', 'creator', 'approver']);

        if (auth()->user()->role === 'Staff' && $transaction->created_by !== auth()->id()) {
            abort(403, 'Akses Ditolak.');
        }

        return view('transactions.show', compact('transaction'));
    }

    public function createIncoming(): View
    {
        if (auth()->user()->role !== 'Staff') {
            abort(403, 'Akses Ditolak: Hanya Staff yang bisa membuat transaksi baru.');
        }
        $suppliers = User::where('role', 'Supplier')->where('is_approved', true)->get();
        $products = Product::all();

        return view('transactions.create_incoming', compact('suppliers', 'products'));
    }

    // Menggunakan TransactionStoreRequest
    public function storeIncoming(TransactionStoreRequest $request): RedirectResponse
    {
        // Authorization sudah di Request::authorize()
        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'transaction_number' => 'IN' . strtoupper(Str::random(10)),
                'type' => 'Incoming',
                'supplier_id' => $request->supplier_id,
                'created_by' => auth()->id(),
                'status' => 'Pending',
                'date' => $request->date,
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                $transaction->items()->create($item);
            }

            DB::commit();

            return redirect()->route('transactions.index')->with('success', 'Transaksi Masuk dibuat, menunggu persetujuan Manager.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan transaksi: ' . $e->getMessage());
        }
    }

    public function createOutgoing(): View
    {
        if (auth()->user()->role !== 'Staff') {
            abort(403, 'Akses Ditolak: Hanya Staff yang bisa membuat transaksi baru.');
        }
        $products = Product::all();
        return view('transactions.create_outgoing', compact('products'));
    }

    // Menggunakan TransactionStoreRequest
    public function storeOutgoing(TransactionStoreRequest $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'transaction_number' => 'OUT-' . time(),
                'type' => 'Outgoing',
                'status' => 'Pending',
                'date' => $request->date,
                'customer_name' => $request->customer_name,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $itemData) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'price_at_transaction' => $itemData['price_at_transaction'],
                ]);
            }

            DB::commit();

            return redirect()->route('transactions.show', $transaction->id)
                ->with('success', 'Transaksi Keluar berhasil dibuat dan menunggu persetujuan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan transaksi: ' . $e->getMessage());
        }
    }

    public function approve(Transaction $transaction)
    {
        $transaction->load('items.product');

        DB::beginTransaction();
        try {
            foreach ($transaction->items as $item) {
                // Hitung delta: positif untuk Incoming, negatif untuk Outgoing
                $delta = $transaction->type === 'Incoming'
                    ? $item->quantity
                    : -$item->quantity;

                // Panggil service untuk update stok + catat StockMovement
                $this->inventory->adjustStock(
                    $item->product,
                    $delta,
                    strtolower($transaction->type), // 'incoming' atau 'outgoing'
                    $transaction                   // reference untuk audit trail
                );
            }

            $transaction->update([
                'status' => 'Approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            DB::commit();
            return redirect()->route('transactions.show', $transaction->id)
                ->with('success', 'Transaksi berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }


    public function reject(Transaction $transaction): RedirectResponse
    {
        if (!in_array(auth()->user()->role, ['Admin', 'Manager'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menolak transaksi.');
        }
        if ($transaction->status !== 'Pending') {
            return redirect()->back()->with('error', 'Transaksi sudah disetujui atau dibatalkan sebelumnya.');
        }

        try {
            $transaction->update([
                'status' => 'Rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            return redirect()->route('transactions.show', $transaction->id)
                ->with('success', 'Transaksi berhasil ditolak.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menolak transaksi: ' . $e->getMessage());
        }
    }

    // Contoh method edit/update yang memakai TransactionUpdateRequest
    public function edit(Transaction $transaction)
    {
        // view edit
    }

    public function update(TransactionUpdateRequest $request, Transaction $transaction): RedirectResponse
    {
        // update hanya field yang diizinkan oleh Request
        DB::beginTransaction();
        try {
            $transaction->update($request->only(['date', 'notes']));

            if ($request->has('items')) {
                // logika update items: hapus & recreate atau sync sesuai kebijakan
            }

            DB::commit();
            return redirect()->route('transactions.show', $transaction->id)->with('success', 'Transaksi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage());
        }
    }
}