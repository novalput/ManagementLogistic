<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnDetail extends Model
{
    /** @use HasFactory<\Database\Factories\ReturnDetailFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'return_id',
        'item_id',
        'quantity',
    ];

    public function returnTransaction(): BelongsTo
    {
        return $this->belongsTo(ReturnTransaction::class, 'return_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
