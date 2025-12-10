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
        'manager_id',
        'order_date',
        'expected_delivery_date',
        'status',
        'notes',
        'confirmed_at',
        'shipped_at',
        'received_at',
        'cancelled_at',
        'cancellation_reason',
        'total_amount', // ✅ TAMBAHKAN jika belum ada
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'received_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    protected $appends = [
        'status_color',
        'is_editable',
        'is_confirmable',
        'is_shippable',
        'is_receivable',
        'is_cancellable',
    ];

    // ============= STATUS CONSTANTS =============
    const STATUS_PENDING = 'Pending';
    const STATUS_CONFIRMED = 'Confirmed';
    const STATUS_IN_TRANSIT = 'In Transit';
    const STATUS_RECEIVED = 'Received';
    const STATUS_CANCELLED = 'Cancelled';

    // ============= RELATIONS =============
    public function items(): HasMany
    {
        return $this->hasMany(RestockItem::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function creator(): BelongsTo
    {
        return $this->manager();
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    // ============= ATTRIBUTE ACCESSORS =============
    public function getTotalAmountAttribute(): float
    {
        if (isset($this->attributes['total_amount'])) {
            return $this->attributes['total_amount'];
        }
        
        return $this->items->sum('subtotal');
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_CONFIRMED => 'info',
            self::STATUS_IN_TRANSIT => 'primary',
            self::STATUS_RECEIVED => 'success',
            self::STATUS_CANCELLED => 'danger',
            default => 'secondary',
        };
    }

    // ============= HELPER METHODS FOR POLICY =============
    public function isCreatedBy(User $user): bool
    {
        return (int) $this->manager_id === (int) $user->id;
    }

    public function isSupplierFor(User $user): bool
    {
        return (int) $this->supplier_id === (int) $user->id;
    }

    public function getIsEditableAttribute(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function getIsConfirmableAttribute(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function getIsShippableAttribute(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function getIsReceivableAttribute(): bool
    {
        return $this->status === self::STATUS_IN_TRANSIT;
    }

    public function getIsCancellableAttribute(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    // ============= BUSINESS LOGIC METHODS =============
    public function confirm(): bool
    {
        if ($this->status === self::STATUS_PENDING) {
            $this->status = self::STATUS_CONFIRMED;
            $this->confirmed_at = now();
            return $this->save();
        }
        return false;
    }

    public function ship(): bool
    {
        if ($this->status === self::STATUS_CONFIRMED) {
            $this->status = self::STATUS_IN_TRANSIT;
            $this->shipped_at = now();
            return $this->save();
        }
        return false;
    }

    public function receive(): bool
    {
        if ($this->status === self::STATUS_IN_TRANSIT) {
            $this->status = self::STATUS_RECEIVED;
            $this->received_at = now();
            return $this->save();
        }
        return false;
    }

    public function cancel(?string $reason = null): bool
    {
        if (in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED])) {
            $this->status = self::STATUS_CANCELLED;
            $this->cancelled_at = now();
            $this->cancellation_reason = $reason;
            return $this->save();
        }
        return false;
    }

    // ============= SCOPES =============
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeInTransit($query)
    {
        return $query->where('status', self::STATUS_IN_TRANSIT);
    }

    public function scopeReceived($query)
    {
        return $query->where('status', self::STATUS_RECEIVED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeForSupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeForManager($query, $managerId)
    {
        return $query->where('manager_id', $managerId);
    }

    // ============= STATIC METHODS =============
    public static function generatePONumber(): string
    {
        $date = now()->format('Ymd');
        
        // Cari PO number tertinggi hari ini
        $lastOrder = self::whereDate('created_at', today())
            ->orderByRaw('CAST(SUBSTRING_INDEX(po_number, "-", -1) AS UNSIGNED) DESC')
            ->first();
        
        // Atau cara sederhana: cari berdasarkan pattern
        $lastOrder = self::where('po_number', 'like', 'PO-' . $date . '-%')
            ->orderBy('po_number', 'desc')
            ->first();
        
        if ($lastOrder) {
            // Extract sequence number
            $parts = explode('-', $lastOrder->po_number);
            $lastSequence = (int) end($parts);
            $sequence = $lastSequence + 1;
        } else {
            $sequence = 1;
        }
        
        return 'PO-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get creator attribute (for backward compatibility)
     */
    public function getCreatorAttribute()
    {
        return $this->manager;
    }

    /**
     * Get created_by attribute (alias)
     */
    public function getCreatedByAttribute()
    {
        return $this->manager_id;
    }
}