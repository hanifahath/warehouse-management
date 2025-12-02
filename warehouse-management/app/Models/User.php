<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// Import Models yang digunakan dalam relasi
use App\Models\RestockOrder;
use App\Models\Transaction;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_approved',
        // 'role' harus dimasukkan di sini jika ingin diisi massal
        'role', 
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
    
    // --- Custom Method ---

    /**
     * Memeriksa apakah pengguna memiliki peran yang diberikan.
     * Menggunakan kolom 'role' dari tabel users (sesuai skema migrasi).
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    // --- Relasi ---

    public function restockOrders()
    {
        // Relasi untuk Supplier
        return $this->hasMany(RestockOrder::class, 'supplier_id');
    }

    public function transactionsCreated()
    {
        // Relasi untuk Staff yang membuat transaksi
        return $this->hasMany(Transaction::class, 'created_by');
    }

    public function transactionsApproved()
    {
        // Relasi untuk Admin/Manager yang menyetujui transaksi
        return $this->hasMany(Transaction::class, 'approved_by');
    }
}