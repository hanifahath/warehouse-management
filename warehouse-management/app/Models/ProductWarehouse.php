<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductWarehouse extends Model
{
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'stock',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // LOGIC INTI INVENTORY:
    public function addStock($qty)
    {
        $this->increment('stock', $qty);
    }

    public function reduceStock($qty)
    {
        if ($this->stock < $qty) {
            throw new \Exception("Not enough stock in warehouse");
        }

        $this->decrement('stock', $qty);
    }

    public function hasStock($qty)
    {
        return $this->stock >= $qty;
    }
}
