<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RestockUpdateRequest;
use App\Http\Requests\RestockReceiveRequest;
use App\Models\RestockOrder;
use App\Models\User;
use App\Models\Product;
use App\Services\RestockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RestockOrderController extends Controller
{
    protected $restockService;

    public function __construct(RestockService $restockService)
    {
        $this->restockService = $restockService;
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', RestockOrder::class);
        
        $suppliers = User::where('role', 'supplier')
            ->where('is_approved', true)
            ->orderBy('name')
            ->get();
        
        $restocks = $this->restockService->getFilteredOrders($request);
        
        $statusOptions = [
            'Pending' => 'Pending',
            'Confirmed' => 'Confirmed by Supplier',
            'In Transit' => 'In Transit',
            'Received' => 'Received',
            'Cancelled' => 'Cancelled',
        ];
        
        $totalOrders = RestockOrder::count();
        $pendingCount = RestockOrder::where('status', 'Pending')->count();
        $confirmedCount = RestockOrder::where('status', 'Confirmed')->count();
        $inTransitCount = RestockOrder::where('status', 'In Transit')->count();
        $receivedCount = RestockOrder::where('status', 'Received')->count();
        $cancelledCount = RestockOrder::where('status', 'Cancelled')->count();
        
        return view('restocks.index', compact(
            'restocks', 
            'suppliers', 
            'statusOptions',
            'totalOrders',
            'pendingCount',
            'confirmedCount',
            'inTransitCount',
            'receivedCount',
            'cancelledCount'
        ));
    }

    public function create()
    {
        $this->authorize('create', RestockOrder::class);
        
        $suppliers = User::where('role', 'supplier')
            ->where('is_approved', true)
            ->orderBy('name')
            ->get();
        
        $products = Product::orderBy('name')
            ->get(['id', 'sku', 'name', 'current_stock', 'purchase_price']);
        
        return view('restocks.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', RestockOrder::class);
        
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|integer|exists:users,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'required|date|after:order_date',
            'notes' => 'nullable|string|max:1000',
            'total_amount' => 'required|numeric|min:0',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|integer|exists:products,id',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|integer|min:1',
        ], [
            'product_id.required' => 'At least one product is required',
            'product_id.min' => 'At least one product is required',
            'product_id.*.required' => 'Product is required',
            'quantity.required' => 'Quantity is required',
            'quantity.*.required' => 'Quantity is required',
            'quantity.*.min' => 'Quantity must be at least 1',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        try {
            $items = [];
            for ($i = 0; $i < count($request->product_id); $i++) {
                $items[] = [
                    'product_id' => $request->product_id[$i],
                    'quantity' => $request->quantity[$i]
                ];
            }
            
            $data = [
                'supplier_id' => $request->supplier_id,
                'order_date' => $request->order_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'notes' => $request->notes,
                'total_amount' => $request->total_amount,
                'items' => $items
            ];
            
            $restock = $this->restockService->createRestockOrder($data);
            
            return redirect()->route('restocks.show', $restock)
                ->with('success', 'Order restock berhasil dibuat. PO Number: ' . $restock->po_number);
                
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal membuat order: ' . $e->getMessage());
        }
    }

    public function show(RestockOrder $restock)
    {
        $this->authorize('view', $restock);
  
        $restock->load([
            'items.product',
            'supplier',
            'manager', 
            'receiver'
        ]);
        
        return view('restocks.show', compact('restock'));
    }

    public function edit(RestockOrder $restock)
    {
        $this->authorize('update', $restock);
        
        $suppliers = User::where('role', 'supplier')
            ->where('is_approved', true)
            ->orderBy('name')
            ->get();
        
        $products = Product::orderBy('name')->get();
        
        return view('restocks.edit', compact('restock', 'suppliers', 'products'));
    }

    public function update(RestockUpdateRequest $request, RestockOrder $restock)
    {
        $this->authorize('update', $restock);
        
        try {
            $updated = $this->restockService->updateRestockOrder($restock, $request->validated());
            
            return redirect()->route('restocks.show', $updated)
                ->with('success', 'Order restock berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui order: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, RestockOrder $restock)
    {
        $this->authorize('delete', $restock);
        
        $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:500'],
        ]);
        
        try {
            $this->restockService->cancelOrder($restock, $request->cancellation_reason);
            
            return redirect()->route('restocks.index')
                ->with('success', 'Order restock berhasil dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus order: ' . $e->getMessage());
        }
    }

    public function confirm(Request $request, $id)
    {
        if ($id instanceof RestockOrder) {
            $restock = $id;
        } else {
            $restock = RestockOrder::findOrFail($id);
        }
        
        $user = auth()->user();
        
        if ($user->role !== 'supplier') {
            return back()->with('error', 'Hanya supplier yang bisa konfirmasi order.');
        }
        
        if (!$user->is_approved) {
            return back()->with('error', 'Akun supplier belum disetujui.');
        }
        
        if ($restock->supplier_id != $user->id) {
            return back()->with('error', 'Ini bukan order Anda.');
        }
        
        if ($restock->status !== 'Pending') {
            return back()->with('error', 'Order sudah tidak dalam status Pending.');
        }
        
        DB::transaction(function () use ($restock) {
            $restock->update([
                'status' => 'Confirmed',
                'confirmed_at' => now(),
                'notes' => ($restock->notes ?? '') . "\n[Dikonfirmasi oleh supplier: " . now()->format('d/m/Y H:i') . "]",
            ]);
        });
        
        return redirect()->route('restocks.show', $restock)
            ->with('success', '✅ Order berhasil dikonfirmasi! Status berubah menjadi Confirmed.');
    }

    public function deliver(Request $request, RestockOrder $restock)
    {
        $this->authorize('deliver', $restock);
        
        try {
            $delivered = $this->restockService->markInTransit($restock);
            
            return redirect()->route('restocks.show', $delivered)
                ->with('success', 'Order berhasil ditandai sebagai dalam pengiriman.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui status pengiriman: ' . $e->getMessage());
        }
    }

    public function receive(RestockReceiveRequest $request, RestockOrder $restock)
    {
        if ($restock->status !== 'In Transit') {
            return back()->with('error', 'Order belum dalam pengiriman.');
        }
        
        $restock->update([
            'status' => 'Received',
            'received_at' => now(),
        ]);
        
        return redirect()->route('restocks.show', $restock)
            ->with('success', 'Order berhasil diterima.');
    }

    public function cancel(Request $request, RestockOrder $restock)
    {
        $this->authorize('cancel', $restock);
        
        $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:500'],
        ]);
        
        try {
            $cancelled = $this->restockService->cancelOrder($restock, $request->cancellation_reason);
            
            return redirect()->route('restocks.show', $cancelled)
                ->with('success', 'Order berhasil dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan order: ' . $e->getMessage());
        }
    }

    public function supplierIndex(Request $request)
    {
        $this->authorize('viewSupplierOrders', RestockOrder::class);
        
        $restocks = $this->restockService->getSupplierOrders($request);
        
        $stats = $this->restockService->getSupplierStats();
        
        return view('supplier.restocks.index', [
            'restocks' => $restocks,
            'totalOrders' => $stats['totalOrders'],
            'pendingCount' => $stats['pendingCount'],
            'confirmedCount' => $stats['confirmedCount'],
            'inTransitCount' => $stats['inTransitCount'],
            'receivedCount' => $stats['receivedCount'],
            'cancelledCount' => $stats['cancelledCount'],
        ]);
    }
}