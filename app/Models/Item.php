<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory;

    protected $fillable = [
        'item_code',
        'item_name',
        'type',
        'stock',
        'condition',
    ];

    /**
     * Get the aggregated stock count from ItemUnits.
     * This will eventually replace the 'stock' column usage.
     */
    public function getAggregatedStockAttribute()
    {
        return $this->units()->where('status', 'available')->where('condition', 'good')->count();
    }
    
    public function getAggregatedDefectStockAttribute()
    {
        return $this->units()->where('condition', 'defect')->count();
    }

    public function units(): HasMany
    {
        return $this->hasMany(ItemUnit::class);
    }

    public function borrowingDetails(): HasMany
    {
        return $this->hasMany(BorrowingDetail::class);
    }

    public function returnDetails(): HasMany
    {
        return $this->hasMany(ReturnDetail::class);
    }
}
