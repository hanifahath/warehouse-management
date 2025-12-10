<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;

class TransactionUpdateRequest extends TransactionRequest
{
    protected function isIncoming(): bool
    {
        // Deteksi dari transaction yang diupdate
        $transaction = $this->route('transaction');
        return $transaction && $transaction->type === 'incoming';
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
            'customer_name' => 'nullable|string|max:255',
        ];
    }
    
    protected function outgoingRules(): array
    {
        $transaction = $this->route('transaction');
        
        return [
            'customer_name' => 'required|string|max:255',
            'items.*.quantity' => array_merge(
                ['required', 'integer', 'min:1'],
                [$this->stockValidationRuleForUpdate($transaction)]
            ),
        ];
    }
    
    private function stockValidationRuleForUpdate(Transaction $transaction): \Closure
    {
        return function ($attribute, $value, $fail) use ($transaction) {
            $index = explode('.', $attribute)[1];
            $productId = $this->input("items.{$index}.product_id");
            
            if ($productId) {
                $product = Product::find($productId);
                
                // Cari quantity sebelumnya
                $oldQuantity = 0;
                $oldItem = $transaction->items->where('product_id', $productId)->first();
                if ($oldItem) {
                    $oldQuantity = $oldItem->quantity;
                }
                
                // Stock yang tersedia setelah revert
                $availableStock = $product->current_stock + $oldQuantity;
                
                if ($availableStock < $value) {
                    $fail("Stok {$product->name} tidak mencukupi. Stok tersedia: {$availableStock}");
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