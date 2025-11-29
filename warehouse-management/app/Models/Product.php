<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    // Pastikan semua field yang digunakan di controller ada di sini
    protected $fillable = [
        'sku', 
        'name', 
        'category_id', 
        'description', 
        'purchase_price', 
        'selling_price', 
        'min_stock', 
        'stock', 
        'unit', 
        'location', 
        'image_path',
        
        // --- PERUBAHAN KRITIS ---
        // Tambahkan supplier_id ke fillable. Kunci asing ini menunjuk ke tabel 'users'.
        'supplier_id', 
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
    
    // --- PERUBAHAN KRITIS ---
    /**
     * Relasi: Produk dimiliki oleh satu Supplier (STI)
     */
    public function supplier(): BelongsTo
    {
        // PENTING: Kita merujuk ke Model Supplier (yang merupakan anak dari User)
        // Model Supplier akan memastikan bahwa user yang terhubung memiliki role = 'Supplier'
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    
    /**
     * Relasi: Riwayat transaksi (Barang Masuk/Keluar)
     */
    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionItem::class); 
    }
    
    /**
     * Relasi: Item Restock Order
     */
    public function restockItems(): HasMany
    {
        return $this->hasMany(RestockItem::class);
    }

    /**
     * Scope untuk produk yang stoknya di bawah minimum
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }

    public function setSkuAttribute($value)
    {
        $this->attributes['sku'] = strtoupper($value);
    }

    public function getSellingPriceFormattedAttribute()
    {
        return number_format($this->selling_price, 2, ',', '.');
    }
}