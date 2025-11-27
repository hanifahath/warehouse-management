<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Warehouse extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'location'];

    public function productWarehouses()
    {
        return $this->hasMany(ProductWarehouse::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_warehouse')
                    ->withPivot('stock')
                    ->withTimestamps();
    }

}