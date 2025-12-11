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
        'is_approved',
        'status',
        'role',
        'company_name',
        'phone',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_approved' => 'boolean',
    ];

    protected $appends = [
        'role_label',
        'status_label',
    ];

    // ============= ROLE CONSTANTS =============
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_STAFF = 'staff';
    const ROLE_SUPPLIER = 'supplier';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    // ============= ATTRIBUTE ACCESSORS =============
    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_MANAGER => 'Manager',
            self::ROLE_STAFF => 'Staff',
            self::ROLE_SUPPLIER => 'Supplier',
            default => 'Unknown',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'Aktif',
            self::STATUS_INACTIVE => 'Nonaktif',
            default => 'Unknown',
        };
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    // ============= ROLE HELPERS (FOR POLICY) =============
    public function hasRole(string $role): bool
    {
        return strtolower(trim($this->role ?? '')) === strtolower(trim($role));
    }

    public function isAdmin(): bool
    {
        return strtolower(trim($this->role ?? '')) === self::ROLE_ADMIN;
    }

    public function isManager(): bool
    {
        return strtolower(trim($this->role ?? '')) === self::ROLE_MANAGER;
    }

    public function isStaff(): bool
    {
        return strtolower(trim($this->role ?? '')) === self::ROLE_STAFF;
    }

    public function isSupplier(): bool
    {
        return strtolower(trim($this->role ?? '')) === self::ROLE_SUPPLIER;
    }

    public function isApprovedSupplier(): bool
    {
        return $this->isSupplier() && $this->is_approved;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array(
            strtolower(trim($this->role ?? '')), 
            array_map('strtolower', array_map('trim', $roles))
        );
    }

    public function hasAllRoles(array $roles): bool
    {
        return count(
            array_intersect(
                [strtolower(trim($this->role ?? ''))], 
                array_map('strtolower', array_map('trim', $roles))
            )
        ) === count($roles);
    }

    // ============= PERMISSION HELPERS =============
    public function canManageProducts(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }

    public function canManageCategories(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }

    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    public function canApproveTransactions(): bool
    {
        return $this->isManager();
    }

    public function canCreateTransactions(): bool
    {
        return $this->isStaff();
    }

    public function canViewReports(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }

    public function canManageRestocks(): bool
    {
        return $this->isManager();
    }

    public function canConfirmRestocks(): bool
    {
        return $this->isApprovedSupplier();
    }

    // ============= RELATIONS =============
    public function restockOrders()
    {
        return $this->hasMany(RestockOrder::class, 'supplier_id');
    }

    public function createdRestockOrders()
    {
        return $this->hasMany(RestockOrder::class, 'manager_id');
    }

    public function transactionsCreated()
    {
        return $this->hasMany(Transaction::class, 'created_by');
    }

    public function transactionsApproved()
    {
        return $this->hasMany(Transaction::class, 'approved_by');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'user_id');
    }

    // ============= SCOPES =============
    public function scopeAdmin($query)
    {
        return $query->where('role', self::ROLE_ADMIN);
    }

    public function scopeManager($query)
    {
        return $query->where('role', self::ROLE_MANAGER);
    }

    public function scopeStaff($query)
    {
        return $query->where('role', self::ROLE_STAFF);
    }

    public function scopeSupplier($query)
    {
        return $query->where('role', self::ROLE_SUPPLIER);
    }

    public function scopeApprovedSupplier($query)
    {
        return $query->supplier()->where('is_approved', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    // ============= BUSINESS METHODS =============
    public function getPendingTransactionsCount(): int
    {
        return $this->transactionsCreated()
            ->where('status', 'pending')
            ->count();
    }

    public function getMonthlyTransactionStats(): array
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        return [
            'total' => $this->transactionsCreated()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'pending' => $this->transactionsCreated()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'pending')
                ->count(),
            'approved' => $this->transactionsCreated()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'approved')
                ->count(),
        ];
    }

    public function canChangeRole(string $newRole, User $currentUser): bool
    {
        if ($this->id === $currentUser->id) {
            return $newRole === 'admin'; 
        }
        
        if ($this->isAdmin()) {
            $adminCount = self::where('role', 'admin')->count();
            
            if ($newRole !== 'admin') {
                if ($adminCount <= 2) {
                    return false;
                }
            }
        }
        
        return true;
    }

    public function canBeDeleted(User $currentUser): bool
    {
        if ($this->id === $currentUser->id) {
            return false;
        }
 
        if ($this->isAdmin()) {
            $adminCount = self::where('role', 'admin')->count();
            return $adminCount > 1; 
        }
        
        return true;
    }
}