<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_approved',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_approved' => 'boolean',
        ];
    }

    /**
     * Relasi: jika user adalah Supplier, ia punya banyak RestockOrder
     */
    public function restockOrders()
    {
        return $this->hasMany(RestockOrder::class, 'supplier_id');
    }

    /**
     * Relasi: transaksi yang dibuat oleh user (Staff/Manager)
     */
    public function transactionsCreated()
    {
        return $this->hasMany(Transaction::class, 'created_by');
    }

    /**
     * Relasi: transaksi yang disetujui oleh user (Manager/Admin)
     */
    public function transactionsApproved()
    {
        return $this->hasMany(Transaction::class, 'approved_by');
    }

    /**
     * Relasi: restock order yang dibuat oleh user (Manager/Admin)
     */
    public function restocksCreated()
    {
        return $this->hasMany(RestockOrder::class, 'created_by');
    }

    /**
     * Relasi: restock order yang diterima oleh user (Staff/Manager)
     */
    public function restocksReceived()
    {
        return $this->hasMany(RestockOrder::class, 'received_by');
    }
}