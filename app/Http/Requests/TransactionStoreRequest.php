<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\Product;

class TransactionStoreRequest extends TransactionRequest
{
    protected function isIncoming(): bool
    {
        // Debug: Log untuk memastikan
        \Log::info('=== TransactionStoreRequest Debug ===');
        \Log::info('Route Name:', [$this->route()->getName() ?? 'null']);
        \Log::info('Path:', [$this->path()]);
        \Log::info('Method:', [$this->method()]);
        
        // Deteksi dari route name
        $isIncoming = str_contains($this->route()->getName() ?? '', 'incoming');
        \Log::info('Is Incoming detected:', [$isIncoming]);
        
        return $isIncoming;
    }
    
    protected function incomingRules(): array
    {
        \Log::info('Applying INCOMING rules');

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
            // 'customer_name' => 'nullable|string|max:255', // Optional untuk incoming
        ];
    }
    
    protected function outgoingRules(): array
    {
        \Log::info('Applying OUTGOING rules');
        
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
            'items.*.price_at_transaction.required' => 'Harga harus diisi.',
            'items.*.price_at_transaction.min' => 'Harga tidak boleh negatif.',
        ];
    }

}