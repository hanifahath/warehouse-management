<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\Product;
use App\Models\RestockOrder;

class TransactionStoreRequest extends TransactionRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    protected function isIncoming(): bool
    {
        $isIncoming = str_contains($this->route()->getName() ?? '', 'incoming');
        return $isIncoming;
    }
    
    protected function incomingRules(): array
    {

        return [
            'supplier_id' => [
                'required', 
                'integer', 
                function ($attribute, $value, $fail) {
                    $supplier = User::where('id', $value)
                        ->where('role', 'supplier')
                        ->where('is_approved', true)
                        ->first();
                    
                    if (!$supplier) {
                        $fail('Supplier tidak valid atau belum disetujui.');
                    }
                }
            ],
            'restock_order_id' => [
                'nullable',
                'integer',
                'exists:restock_orders,id',
                function ($attribute, $value, $fail) {
                    if (!$value) return;
                    
                    $restockOrder = RestockOrder::find($value);
                    
                    if (strtolower($restockOrder->status) !== 'received') {
                        $fail('Restock order harus berstatus "Received" untuk bisa diproses.');
                    }
                    
                    if ($restockOrder->transactions()->exists()) {
                        $fail('Restock order ini sudah memiliki transaksi penerimaan.');
                    }
                }
            ],
        ];
    }
    
    protected function outgoingRules(): array
    {
        
        return [
            'customer_name' => 'required|string|max:255',
            'items.*.quantity' => array_merge(
                ['required', 'integer', 'min:1'],
                [$this->stockValidationRule()]
            ),
        ];
    }
    
    private function stockValidationRule(): \Closure
    {
        return function ($attribute, $value, $fail) {
            $index = explode('.', $attribute)[1];
            $productId = $this->input("items.{$index}.product_id");
            
            if ($productId) {
                $product = Product::find($productId);
                if ($product && $product->current_stock < $value) {
                    $fail("Stok {$product->name} tidak mencukupi. Stok tersedia: {$product->current_stock}");
                }
            }
        };
    }
    
    // Tambah method ini untuk implement abstract parent class
    protected function commonRules(): array
    {
        return [
            'date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }
    
    public function messages()
    {
        return [
            'date.required' => 'Tanggal transaksi wajib diisi.',
            'supplier_id.required' => 'Supplier wajib dipilih untuk transaksi masuk.',
            'customer_name.required' => 'Nama customer wajib diisi untuk transaksi keluar.',
            'items.required' => 'Minimal satu item harus ditambahkan.',
            'items.*.product_id.required' => 'Produk harus dipilih.',
            'items.*.quantity.required' => 'Quantity harus diisi.',
            'items.*.quantity.min' => 'Quantity minimal 1.',
            'restock_order_id.exists' => 'Restock order tidak ditemukan.',
        ];
    }
}