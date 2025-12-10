<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    // ============= DATABASE STRUCTURE =============
    // Kolom database yang ada:
    // id, product_id, change, source_type, source_id, before_qty, after_qty, performed_by, created_at, updated_at
    
    protected $table = 'stock_movements';
    
    protected $fillable = [
        'product_id',
        'change',           // integer (+ untuk masuk, - untuk keluar)
        'source_type',      // string (contoh: "restock", "transaction")
        'source_id',        // integer (ID dari source)
        'before_qty',       // integer (stok sebelum)
        'after_qty',        // integer (stok setelah)
        'performed_by',     // integer (user_id)
    ];

    protected $casts = [
        'change' => 'integer',
        'before_qty' => 'integer',
        'after_qty' => 'integer',
    ];

    protected $appends = [
        'type',              // 'in' atau 'out' (dihitung dari change)
        'quantity',          // nilai absolut dari change
        'type_label',        // 'Stock In' atau 'Stock Out'
        'change_direction',  // 'increase' atau 'decrease'
        'is_increase',       // boolean
        'is_decrease',       // boolean
        'quantity_change',   // '+20' atau '-5'
        'reference_type',    // alias untuk source_type (untuk kompatibilitas)
        'reference_id',      // alias untuk source_id
        'before_quantity',   // alias untuk before_qty
        'after_quantity',    // alias untuk after_qty
        'user_id',           // alias untuk performed_by
    ];

    // ============= RELATIONS =============
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo('reference', 'source_type', 'source_id');
    }

    // ============= ATTRIBUTE ACCESSORS =============
    public function getTypeAttribute(): string
    {
        // Data ada masalah: change = 0, tapi from 15 to 35
        // Kita hitung dari before_qty dan after_qty jika change = 0
        if ($this->change == 0) {
            return $this->after_qty >= $this->before_qty ? 'in' : 'out';
        }
        
        return $this->change > 0 ? 'in' : 'out';
    }

    public function getQuantityAttribute(): int
    {
        // Jika change = 0, hitung dari selisih before/after
        if ($this->change == 0) {
            return abs($this->after_qty - $this->before_qty);
        }
        
        return abs($this->change);
    }

    public function getTypeLabelAttribute(): string
    {
        if (in_array($this->source_type, ['restock', 'transaction_in'])) {
            return 'Stock In';
        }
        return 'Stock Out';
    }

    public function getChangeDirectionAttribute(): string
    {
        if (in_array($this->source_type, ['restock', 'transaction_in'])) {
            return 'increase';
        }
        return 'decrease';
    }

    public function getIsIncreaseAttribute(): bool
    {
        return in_array($this->source_type, ['restock', 'transaction_in']);
    }

    public function getIsDecreaseAttribute(): bool
    {
        return $this->source_type === 'transaction_out';
    }

    public function getQuantityChangeAttribute(): string
    {
        $prefix = $this->is_increase ? '+' : '-';
        return $prefix . $this->quantity;
    }

    public function getReferenceTypeAttribute()
    {
        return $this->source_type;
    }

    public function setReferenceTypeAttribute($value)
    {
        $this->attributes['source_type'] = $value;
    }

    public function getReferenceIdAttribute()
    {
        return $this->source_id;
    }

    public function setReferenceIdAttribute($value)
    {
        $this->attributes['source_id'] = $value;
    }

    public function getBeforeQuantityAttribute(): int
    {
        return $this->before_qty;
    }

    public function setBeforeQuantityAttribute($value)
    {
        $this->attributes['before_qty'] = $value;
    }

    public function getAfterQuantityAttribute(): int
    {
        return $this->after_qty;
    }

    public function setAfterQuantityAttribute($value)
    {
        $this->attributes['after_qty'] = $value;
    }

    public function getUserIdAttribute()
    {
        return $this->performed_by;
    }

    public function setUserIdAttribute($value)
    {
        $this->attributes['performed_by'] = $value;
    }

    // ============= HELPER METHODS =============
    public static function log(
        Product $product,
        string $type, // 'in' or 'out'
        int $quantity,
        ?Model $reference = null,
        ?User $user = null
    ): StockMovement {
        // Calculate change: positive for in, negative for out
        $change = $type === 'in' ? $quantity : -$quantity;
        
        return self::create([
            'product_id' => $product->id,
            'change' => $change, // stored as change in database
            'source_type' => $reference ? get_class($reference) : null,
            'source_id' => $reference ? $reference->id : null,
            'before_qty' => $product->current_stock,
            'after_qty' => $type === 'in' 
                ? $product->current_stock + $quantity 
                : $product->current_stock - $quantity,
            'performed_by' => $user ? $user->id : auth()->id(),
        ]);
    }

    // ============= SCOPES =============
    public function scopeIn($query)
    {
        // Karena data mungkin salah (change = 0), kita gunakan logika yang sama
        return $query->where(function($q) {
            $q->where('change', '>', 0)
              ->orWhere(function($q2) {
                  $q2->where('change', 0)
                     ->whereColumn('after_qty', '>', 'before_qty');
              });
        });
    }

    public function scopeOut($query)
    {
        return $query->where(function($q) {
            $q->where('change', '<', 0)
              ->orWhere(function($q2) {
                  $q2->where('change', 0)
                     ->whereColumn('after_qty', '<', 'before_qty');
              });
        });
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('performed_by', $userId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    // ============= FIX DATA YANG SALAH =============
    /**
     * Fix incorrect data where change = 0 but quantities changed
     */
    public static function fixIncorrectData(): void
    {
        $incorrect = self::where('change', 0)
            ->whereColumn('after_qty', '!=', 'before_qty')
            ->get();
        
        foreach ($incorrect as $movement) {
            $correctChange = $movement->after_qty - $movement->before_qty;
            $movement->update(['change' => $correctChange]);
        }
    }
    
    /**
     * Get reference info for display
     */
    public function getReferenceInfoAttribute(): ?string
    {
        if (!$this->reference) {
            return null;
        }
        
        if ($this->source_type === 'App\Models\Transaction') {
            return 'Transaksi: ' . $this->reference->transaction_number;
        }
        
        if ($this->source_type === 'App\Models\RestockOrder') {
            return 'Restock: ' . $this->reference->po_number;
        }
        
        return $this->source_type;
    }
}