<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_number',
        'type',
        'supplier_id',
        'created_by',
        'approved_by', // KRITIS: Tambahkan approved_by
        'customer_name',
        'status',
        'date',
        'approved_at', // KRITIS: Tambahkan approved_at
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'approved_at' => 'datetime',
    ];


    /**
     * Relasi 1:N ke Detail Item Transaksi
     */
    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Relasi N:1 ke Pembuat Transaksi (Staff)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Relasi N:1 ke Penyedia (Supplier) - Hanya untuk Incoming
     */
    public function supplier(): BelongsTo
    {
        // FIX: supplier_id menunjuk ke tabel users
        return $this->belongsTo(User::class, 'supplier_id');
    }
    
    /**
     * Relasi N:1 ke Penyutuju (Manager/Admin) - Hanya untuk status Approved/Rejected
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}