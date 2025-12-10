<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 
        'description',
        'image_path',
    ];

    protected $appends = ['image_url']; // Tambahkan ini

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }
        
        return \Storage::url($this->image_path);
    }

    /**
     * Get products count attribute
     */
    public function getProductsCountAttribute(): int
    {
        // Use loaded count if available, otherwise query
        if (array_key_exists('products_count', $this->attributes)) {
            return $this->attributes['products_count'];
        }
        
        return $this->products()->count();
    }
}