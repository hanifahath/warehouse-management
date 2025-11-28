<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionItem extends Model
{
    protected $fillable = [
        'transaction_id',
        'product_id',
        'quantity',
        'price_at_transaction', // KRITIS: Tambahkan harga saat transaksi
        // 'warehouse_id' (dihapus karena menggunakan stok tunggal di Product)
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_at_transaction' => 'float',
    ];
    
    /**
     * Relasi N:1 ke Header Transaksi
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Relasi N:1 ke Produk
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    // Relasi warehouse dan fungsi applyStock Dihapus/diabaikan
    // karena sistem stok tunggal digunakan di Model Product.
}