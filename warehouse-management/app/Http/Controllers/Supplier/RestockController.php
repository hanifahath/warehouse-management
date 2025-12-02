<?php

namespace App\Http\Controllers\Supplier;

use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\RestockStoreRequest;
use App\Http\Requests\RestockUpdateRequest;
use App\Http\Requests\RestockUpdateStatusRequest;
use App\Models\RestockOrder;
use App\Models\User;
use App\Models\Product;
use App\Services\RestockService;

class RestockController extends Controller
{
    protected $service;

    public function __construct(RestockService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $orders = RestockOrder::with('items.product')->latest()->paginate(10);
        return view('restocks.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = User::role('Supplier')->get();
        $products = Product::all();
        return view('restocks.create', compact('suppliers', 'products'));
    }

    public function store(RestockStoreRequest $request)
    {
        $order = $this->service->createRestock($request->validated());
        return redirect()->route('restocks.index')->with('success', 'PO berhasil dibuat.');
    }

    public function edit(RestockOrder $restockOrder)
    {
        $suppliers = User::role('Supplier')->get();
        $products = Product::all();
        $restockOrder->load('items.product');
        return view('restocks.edit', compact('restockOrder', 'suppliers', 'products'));
    }

    public function update(RestockUpdateRequest $request, RestockOrder $restockOrder)
    {
        $this->service->updateRestock($restockOrder, $request->validated());
        return redirect()->route('restocks.index')->with('success', 'PO berhasil diperbarui.');
    }

    public function updateStatus(RestockUpdateStatusRequest $request, RestockOrder $restockOrder)
    {
        $this->service->updateStatus($restockOrder, $request->input('status'));
        return redirect()->route('restocks.index')->with('success', 'Status PO berhasil diupdate.');
    }

    public function confirm(RestockOrder $restockOrder)
    {
        if (!Gate::allows('confirm', $restockOrder)) {
            abort(403, 'Hanya Supplier terkait yang dapat mengkonfirmasi PO ini.');
        }

        $restockOrder->update([
            'status' => 'Confirmed by Supplier',
            'confirmed_at' => now(),
        ]);

        return redirect()->route('restocks.index')->with('success', 'PO berhasil dikonfirmasi.');
    }

    public function receive(RestockOrder $restockOrder)
    {
        if (!Gate::allows('receive', $restockOrder)) {
            abort(403, 'Hanya Staff/Manager yang bisa menerima PO ini.');
        }

        app(\App\Services\RestockService::class)->updateStatus($restockOrder, 'Received');

        return redirect()->route('restocks.index')->with('success', 'PO diterima dan transaksi masuk dibuat.');
    }
}
