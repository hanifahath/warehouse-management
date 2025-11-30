<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
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

    public function restockOrders()
    {
        return $this->hasMany(RestockOrder::class, 'supplier_id');
    }

    public function transactionsCreated()
    {
        return $this->hasMany(Transaction::class, 'created_by');
    }

    public function transactionsApproved()
    {
        return $this->hasMany(Transaction::class, 'approved_by');
    }
}
