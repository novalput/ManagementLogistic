<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BorrowingDetail extends Model
{
    /** @use HasFactory<\Database\Factories\BorrowingDetailFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'borrowing_id',
        'item_id',
        'quantity',
        'returned_quantity',
    ];

    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
