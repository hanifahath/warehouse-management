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
        // 'created_by',
        'order_date',
        'expected_delivery_date',
        'status',
        'notes',
        'received_at', // KRITIS: Tambahkan received_at
        // 'received_by', // KRITIS: Tambahkan received_by
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'received_at' => 'datetime',
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

    /**
     * Relasi N:1 ke Penerima Order (Staff/Manager)
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'po_number', 'po_number');
    }
}