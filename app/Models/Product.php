<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku', 
        'name', 
        'category_id', 
        'description', 
        'purchase_price', 
        'selling_price', 
        'min_stock', 
        'current_stock', // ✅ PASTIKAN ini bukan 'stock'
        'unit', 
        'rack_location', 
        'image_path',
    ];

    protected $casts = [
        'current_stock' => 'integer', // ✅ PASTIKAN ini
        'min_stock' => 'integer',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    protected $appends = [
        'stock_status',
        'stock_value',
        'is_low_stock',
        'is_out_of_stock',
    ];

    // ============= RELATIONS =============
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function restockItems(): HasMany
    {
        return $this->hasMany(RestockItem::class);
    }

    // ============= ATTRIBUTE ACCESSORS =============
    public function getStockStatusAttribute(): string
    {
        if ($this->current_stock == 0) {
            return 'out_of_stock';
        }
        
        if ($this->current_stock <= $this->min_stock) {
            return 'low_stock';
        }
        
        return 'healthy';
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->current_stock > 0 && $this->current_stock <= $this->min_stock;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->current_stock == 0;
    }

    public function getStockValueAttribute(): float
    {
        return $this->current_stock * $this->purchase_price;
    }

    /**
     * Convenience accessor used by views: returns the stored image path.
     * Views call `asset('storage/' . $product->image_url)`, so this
     * should return the relative path (e.g. "products/abc.jpg").
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ?: null;
    }

    public function getSellingPriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->selling_price, 0, ',', '.');
    }

    public function getPurchasePriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->purchase_price, 0, ',', '.');
    }

    public function getStockValueFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->stock_value, 0, ',', '.');
    }

    public function getStockDeficitAttribute(): int
    {
        return max(0, $this->min_stock - $this->current_stock);
    }

    // ============= HELPER METHODS =============
    public function increaseStock(int $quantity): bool
    {
        $this->current_stock += $quantity;
        return $this->save();
    }

    public function decreaseStock(int $quantity): bool
    {
        if ($this->current_stock >= $quantity) {
            $this->current_stock -= $quantity;
            return $this->save();
        }
        return false;
    }

    public function updateStock(int $newStock): bool
    {
        $this->current_stock = $newStock;
        return $this->save();
    }

    public function hasSufficientStock(int $quantity): bool
    {
        return $this->current_stock >= $quantity;
    }

    // ============= SCOPES =============
    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'min_stock')
            ->where('current_stock', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', 0);
    }

    public function scopeHealthyStock($query)
    {
        return $query->whereColumn('current_stock', '>', 'min_stock');
    }

    public function scopeCriticalStock($query)
    {
        return $query->whereColumn('current_stock', '<=', \DB::raw('min_stock * 0.5'))
            ->where('current_stock', '>', 0);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // ============= MUTATORS =============
    public function setSkuAttribute($value)
    {
        $this->attributes['sku'] = strtoupper(preg_replace('/\s+/', '', $value));
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords(strtolower($value));
    }
}