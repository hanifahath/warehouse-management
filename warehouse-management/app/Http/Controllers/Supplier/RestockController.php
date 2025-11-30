<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Http\Requests\RestockStoreRequest;
use App\Http\Requests\RestockUpdateRequest;
use Illuminate\Http\Request;
use App\Models\RestockOrder;
use App\Models\RestockItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class RestockController extends Controller
{
    public function index()
    {
        $orders = RestockOrder::with('items.product')->latest()->paginate(10);
        return view('restocks.index', compact('orders'));
    }

    public function show(RestockOrder $restockOrder)
    {
        $restockOrder->load('items.product');
        return view('restocks.show', compact('restockOrder'));
    }

    public function create()
    {
        // Hanya Manager/Admin yang boleh mengakses route ini (atur middleware di routes)
        $suppliers = User::role('Supplier')->get();
        $products = Product::all();

        return view('restocks.create', [
            'suppliers' => $suppliers,
            'products'  => $products,
        ]);
    }

    // Menggunakan RestockStoreRequest untuk validasi & authorize
    public function store(RestockStoreRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $poNumber = 'PO-' . now()->format('YmdHis');

            $order = RestockOrder::create([
                'po_number' => $poNumber,
                'supplier_id' => $data['supplier_id'],
                // 'created_by' => auth()->id(),
                'order_date' => $data['order_date'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => 'Pending',
            ]);

            // Simpan semua item (bukan hanya index 0)
            foreach ($data['items'] as $item) {
                RestockItem::create([
                    'restock_order_id' => $order->id,
                    'product_id'       => $item['product_id'],
                    'unit_price'       => $item['unit_price'],
                    'quantity'         => $item['quantity'],
                    'subtotal'         => $item['unit_price'] * $item['quantity'],
                ]);
            }

            DB::commit();

            return redirect()->route('supplier.restocks.index')->with('success', 'Purchase Order berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return back()->with('error', 'Gagal membuat Purchase Order.')->withInput();
        }
    }

    // Supplier mengkonfirmasi PO (Pending → Confirmed by Supplier)
    public function confirm(RestockOrder $restockOrder)
    {
        // Otorisasi: pastikan route/middleware membatasi hanya Supplier terkait atau gunakan Request khusus
        if ($restockOrder->status !== 'Pending') {
            return back()->with('error', 'Pesanan sudah diproses sebelumnya.');
        }

        try {
            $restockOrder->update([
                'status' => 'Confirmed by Supplier',
            ]);

            return back()->with('success', 'Order berhasil dikonfirmasi oleh Supplier.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Gagal mengkonfirmasi order.');
        }
    }

    // Gudang menerima barang (Confirmed by Supplier → Received) + update stok
    public function receive(RestockOrder $restockOrder)
    {
        // Otorisasi: pastikan hanya Manager/Staff yang berwenang (atur di routes/middleware)
        if ($restockOrder->status !== 'Confirmed by Supplier') {
            return back()->with('error', 'Pesanan belum dikonfirmasi Supplier.');
        }

        DB::beginTransaction();
        try {
            $restockOrder->update([
                'status'      => 'Received',
                'received_at' => now(),
                // 'received_by' => auth()->id(),
            ]);

            // Gunakan relasi items (pastikan RestockOrder->items relasi benar)
            foreach ($restockOrder->items as $detail) {
                $product = Product::find($detail->product_id);
                if (!$product) {
                    DB::rollBack();
                    return back()->with('error', 'Produk tidak ditemukan.');
                }

                $product->stock += $detail->quantity;
                $product->save();
            }

            DB::commit();
            return back()->with('success', 'Barang berhasil diterima.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return back()->with('error', 'Gagal memproses penerimaan order.');
        }
    }

    // Optional: edit/update PO (gunakan RestockUpdateRequest jika perlu)
    public function edit(RestockOrder $restockOrder)
    {
        $suppliers = User::role('Supplier')->get();
        $products = Product::all();
        $restockOrder->load('items.product');

        return view('restocks.edit', compact('restockOrder', 'suppliers', 'products'));
    }

    public function update(RestockUpdateRequest $request, RestockOrder $restockOrder)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $restockOrder->update([
                'supplier_id' => $data['supplier_id'],
                'order_date' => $data['order_date'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'notes' => $data['notes'] ?? null,
                // jangan ubah status di sini kecuali memang diizinkan
            ]);

            if (isset($data['items'])) {
                // strategi: hapus item lama dan recreate (atau implementasikan sync lebih canggih)
                $restockOrder->items()->delete();
                foreach ($data['items'] as $item) {
                    RestockItem::create([
                        'restock_order_id' => $restockOrder->id,
                        'product_id'       => $item['product_id'],
                        'unit_price'       => $item['unit_price'],
                        'quantity'         => $item['quantity'],
                        'subtotal'         => $item['unit_price'] * $item['quantity'],
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('supplier.restocks.show', $restockOrder->id)->with('success', 'Purchase Order berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return back()->with('error', 'Gagal memperbarui Purchase Order.')->withInput();
        }
    }
}