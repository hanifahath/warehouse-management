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
        'stock', 
        'unit', 
        'location', 
        'image_path',
    ];

    protected $casts = [
        'stock' => 'integer',
        'min_stock' => 'integer',
        'purchase_price' => 'float',
        'selling_price' => 'float',
    ];

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
