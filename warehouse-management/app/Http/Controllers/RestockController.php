<?php

namespace App\Http\Controllers;

use App\Models\RestockOrder;
use App\Models\User;
use App\Services\RestockService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Product;

class RestockController extends Controller
{
    protected $restockService;
    protected $transactionService;

    public function __construct(RestockService $restockService, TransactionService $transactionService)
    {
        $this->restockService = $restockService;
        $this->transactionService = $transactionService;
        
        // ✅ HAPUS SEMUA MIDDLEWARE DI SINI
        // Middleware sudah dihandle di routes/web.php
    }

    // --- UMUM & LISTING ---

    public function index()
    {
        // Logika filter/scope untuk Manager/Supplier (hanya order mereka)
        if (auth()->user()->role === 'Supplier') {
            $orders = RestockOrder::where('supplier_id', auth()->id())
                                ->with(['supplier', 'creator'])
                                ->latest()
                                ->paginate(10);
        } else {
            // Admin, Manager, Staff melihat semua
            $orders = RestockOrder::with(['supplier', 'creator'])
                                ->latest()
                                ->paginate(10);
        }
        
        return view('restocks.index', compact('orders'));
    }
    
    public function show(RestockOrder $restockOrder)
    {
        return view('restocks.show', compact('restockOrder'));
    }
    
    // --- AKSI MANAGER/ADMIN (CREATE & STORE) ---
    
    public function create()
    {
        $suppliers = User::where('role', 'Supplier')->where('is_approved', true)->get();
        $products = Product::all(); 
        
        return view('restocks.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:users,id',
            'expected_delivery_date' => 'required|date|after_or_equal:today',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ], [
            'items.min' => 'Anda harus menambahkan minimal satu item pesanan.'
        ]);
        
        $poNumber = 'PO-' . strtoupper(Str::random(8));
        
        try {
            $this->restockService->createOrder(
                $request->only(['supplier_id', 'expected_delivery_date', 'items']), 
                auth()->id(), 
                $poNumber
            );
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal membuat PO: ' . $e->getMessage());
        }

        return redirect()->route('restocks.index')->with('success', "Purchase Order {$poNumber} berhasil dibuat dan dikirim ke Supplier.");
    }

    public function receive(RestockOrder $order)
    {
        if ($order->status !== 'Confirmed by Supplier' && $order->status !== 'In Transit') {
            return back()->with('error', "Order belum dikonfirmasi atau status tidak valid untuk penerimaan.");
        }
        
        try {
            $this->restockService->processReceiving($order, auth()->id());
            
            return back()->with('success', 'Barang diterima. Stok produk telah diperbarui melalui Transaksi Masuk.');

        } catch (\Exception $e) {
            return back()->with('error', 'Penerimaan gagal: ' . $e->getMessage());
        }
    }

    // --- AKSI SUPPLIER ---

    public function confirm(RestockOrder $order)
    {
        if ($order->supplier_id !== auth()->id()) {
            abort(403, 'Anda tidak berhak mengkonfirmasi order ini.');
        }

        if ($order->status === 'Pending') {
            $order->status = 'Confirmed by Supplier';
            $order->confirmed_at = now(); 
            $order->save();
            return back()->with('success', 'Order berhasil dikonfirmasi dan siap dikirim.');
        }

        return back()->with('error', 'Order sudah dikonfirmasi atau tidak lagi Pending.');
    }
}