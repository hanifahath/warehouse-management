<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestockItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'restock_order_id',
        'product_id',
        'quantity',
        'unit_price', // KRITIS: Tambahkan unit_price
        'subtotal', // KRITIS: Tambahkan subtotal
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'float',
        'subtotal' => 'float',
    ];

    /**
     * Relasi N:1 ke Header Restock Order
     */
    public function restockOrder(): BelongsTo
    {
        return $this->belongsTo(RestockOrder::class);
    }
    
    /**
     * Relasi N:1 ke Produk
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}