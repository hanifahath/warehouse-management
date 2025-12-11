<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    protected $table = 'stock_movements';
    
    protected $fillable = [
        'product_id',
        'change',          
        'source_type',      
        'source_id',        
        'before_qty',       
        'after_qty',        
        'performed_by',    
    ];

    protected $casts = [
        'change' => 'integer',
        'before_qty' => 'integer',
        'after_qty' => 'integer',
    ];

    protected $appends = [
        'type',              
        'quantity',          
        'type_label',       
        'change_direction',  
        'is_decrease',       
        'quantity_change',   
        'reference_type',    
        'reference_id',      
        'before_quantity',   
        'after_quantity',    
        'user_id',           
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
        if ($this->change == 0) {
            return $this->after_qty >= $this->before_qty ? 'in' : 'out';
        }
        
        return $this->change > 0 ? 'in' : 'out';
    }

    public function getQuantityAttribute(): int
    {
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
        string $type, 
        int $quantity,
        ?Model $reference = null,
        ?User $user = null
    ): StockMovement {
        $change = $type === 'in' ? $quantity : -$quantity;
        
        return self::create([
            'product_id' => $product->id,
            'change' => $change, 
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