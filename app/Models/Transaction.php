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
        'approved_by',
        'customer_name',
        'status',
        'date',
        'approved_at',
        'notes',
        'rejection_reason', 
        'completed_at',     
        'shipped_at',      
        'total_amount',    
        'restock_order_id',
        'restock_status',
    ];

    protected $casts = [
        'date' => 'date',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    // ============= RELATIONS =============
    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function stockMovements()
    {
        return $this->morphMany(StockMovement::class, 'reference');
    }

    public function restockOrder(): BelongsTo
    {
        return $this->belongsTo(RestockOrder::class, 'restock_order_id');
    }

    // ============= ATTRIBUTE ACCESSORS =============
    public function getTotalAmountAttribute()
    {
        $dbValue = $this->attributes['total_amount'] ?? null;
        
        if (!is_null($dbValue) && $dbValue > 0) {
            return $dbValue;
        }

        if (method_exists($this, 'items')) {
            if (!$this->relationLoaded('items')) {
                $this->load('items');
            }
            
            if ($this->items->isNotEmpty()) {
                $calculated = $this->items->sum(function($item) {
                    return ($item->quantity ?? 0) * ($item->price_at_transaction ?? $item->product->price ?? 0);
                });
                
                if ($calculated > 0 && is_null($dbValue)) {
                    $this->update(['total_amount' => $calculated]);
                }
                
                return $calculated;
            }
        }
        
        return 0;
    }
    
    public function getStatusColorAttribute(): string
    {
        return match(strtolower($this->status)) {
            'pending' => 'warning',
            'approved' => 'info',
            'verified' => 'primary',
            'completed' => 'success',
            'shipped' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->isIncoming() ? 'Masuk' : 'Keluar';
    }

    public function getIsIncomingAttribute(): bool
    {
        return strtolower($this->type) === 'incoming';
    }
    
    public function getIsOutgoingAttribute(): bool
    {
        return strtolower($this->type) === 'outgoing';
    }
    
    public function setTypeAttribute($value)
    {
        $this->attributes['type'] = strtolower($value);
    }

    // ============= HELPER METHODS FOR POLICY =============
    public function isCreatedBy(User $user): bool
    {
        return (int) $this->created_by === (int) $user->id;
    }

    public function isSupplierFor(User $user): bool
    {
        return (int) $this->supplier_id === (int) $user->id;
    }

    public function isPending(): bool
    {
        return strtolower($this->status) === 'pending';
    }

    public function isApproved(): bool
    {
        return strtolower($this->status) === 'approved';
    }

    public function isRejected(): bool
    {
        return strtolower($this->status) === 'rejected';
    }

    public function isVerified(): bool
    {
        return strtolower($this->status) === 'verified';
    }

    public function isCompleted(): bool
    {
        return strtolower($this->status) === 'completed';
    }

    public function isShipped(): bool
    {
        return strtolower($this->status) === 'shipped';
    }

    public function isIncoming(): bool
    {
        return strtolower($this->type) === 'incoming';
    }

    public function isOutgoing(): bool
    {
        return strtolower($this->type) === 'outgoing';
    }

    public function isRestockRelated(): bool
    {
        return !is_null($this->restock_order_id);
    }

    public function canBeEdited(): bool
    {
        return $this->isPending() && empty($this->approved_by);
    }

    public function canBeDeleted(): bool
    {
        return $this->isPending() && empty($this->approved_by);
    }

    public function canBeApproved(): bool
    {
        return $this->isPending();
    }

    public function canBeVerified(): bool
    {
        return $this->isIncoming() && ($this->isApproved() || $this->isPending());
    }

    // ============= BUSINESS LOGIC METHODS =============
    public function updateStatus(string $status, ?array $additionalData = []): bool
    {
        return $this->update(array_merge([
            'status' => ucfirst(strtolower($status)),
        ], $additionalData));
    }
    
    public function approve(User $approver, ?string $notes = null): bool
    {
        $this->status = 'approved';
        $this->approved_by = $approver->id;
        $this->approved_at = now();
        $this->notes = $notes;
        
        return $this->save();
    }

    public function reject(User $rejecter, string $reason): bool
    {
        $this->status = 'rejected';
        $this->approved_by = $rejecter->id;
        $this->approved_at = now();
        $this->rejection_reason = $reason;
        
        return $this->save();
    }

    public function markAsVerified(): bool
    {
        if ($this->isIncoming()) {
            $this->status = 'verified';
            $this->completed_at = now();
            return $this->save();
        }
        return false;
    }

    public function markAsCompleted(): bool
    {
        $this->status = 'completed';
        $this->completed_at = now();
        return $this->save();
    }

    public function markAsShipped(): bool
    {
        if ($this->isOutgoing()) {
            $this->status = 'shipped';
            $this->shipped_at = now();
            return $this->save();
        }
        return false;
    }

    // ============= SCOPES =============
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRestockRelated($query)
    {
        return $query->whereNotNull('restock_order_id');
    }

    public function scopeNotRestockRelated($query)
    {
        return $query->whereNull('restock_order_id');
    }
    
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeIncoming($query)
    {
        return $query->where('type', 'incoming');
    }

    public function scopeOutgoing($query)
    {
        return $query->where('type', 'outgoing');
    }

    public function scopeForSupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId)->incoming();
    }

    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    // ============= STATIC METHODS =============
    public static function generateTransactionNumber(string $type = 'incoming'): string
    {
        $prefix = $type === 'incoming' ? 'IN' : 'OUT';
        $date = now()->format('Ymd');
        $last = self::whereDate('created_at', today())
            ->where('type', $type)
            ->latest('id')
            ->first();
        
        $sequence = $last ? ((int) substr($last->transaction_number, -4)) + 1 : 1;
        
        return $prefix . '-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}