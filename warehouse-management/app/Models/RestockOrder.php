<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestockOrder extends Model
{
    protected $fillable = [
        'po_number',
        'supplier_id',
        'created_by',
        'order_date',
        'expected_delivery_date',
        'status',
        'notes',
    ];

    /**
     * Relasi 1:N ke Item Restock
     */
    public function items(): HasMany
    {
        return $this->hasMany(RestockItem::class);
    }

    /**
     * Relasi N:1 ke Supplier (User dengan role 'Supplier')
     */
    public function supplier(): BelongsTo
    {
        // supplier_id menunjuk ke tabel users
        return $this->belongsTo(User::class, 'supplier_id');
    }
    
    /**
     * Relasi N:1 ke Pembuat Order (Manager)
     */
    public function creator(): BelongsTo
    {
        // created_by menunjuk ke tabel users
        return $this->belongsTo(User::class, 'created_by');
    }
}