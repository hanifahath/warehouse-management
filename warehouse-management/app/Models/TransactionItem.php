<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = [
        'transaction_id',
        'product_id',
        'warehouse_id',
        'quantity',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function applyStock()
    {
        $pw = ProductWarehouse::firstOrCreate(
            [
                'product_id' => $this->product_id,
                'warehouse_id' => $this->warehouse_id,
            ],
            [
                'stock' => 0
            ]
        );

        if ($this->transaction->type === 'Incoming') {
            $pw->increment('stock', $this->quantity);
        } else {
            if ($pw->stock < $this->quantity) {
                throw new \Exception("Not enough stock in warehouse");
            }
            $pw->decrement('stock', $this->quantity);
        }
    }

}
