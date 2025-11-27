<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    // Semua field dari Product dan ProductDetail yang diintegrasikan.
    protected $fillable = [
        'sku', 
        'name', 
        'category_id', 
        'description', 
        'purchase_price', 
        'selling_price', 
        'min_stock', 
        'stock', // Menggunakan 'stock' sebagai current_stock
        'unit', 
        'location', 
        'image_path' // Diganti dari 'image_url' untuk konsistensi path
    ];
    
    // Cast 'stock' dan 'min_stock' ke integer
    protected $casts = [
        'stock' => 'integer',
        'min_stock' => 'integer',
        'purchase_price' => 'float',
        'selling_price' => 'float',
    ];

    /**
     * Relasi: Produk dimiliki oleh satu Kategori
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    
    /**
     * Relasi: Riwayat transaksi (Barang Masuk/Keluar)
     */
    public function transactionDetails(): HasMany
    {
        // Asumsi model detail transaksi Anda bernama TransactionItem
        return $this->hasMany(TransactionItem::class); 
    }
    
    /**
     * Scope untuk produk yang stoknya di bawah minimum
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }
}