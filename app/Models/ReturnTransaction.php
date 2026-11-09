<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnTransaction extends Model
{
    /** @use HasFactory<\Database\Factories\ReturnTransactionFactory> */
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'borrowing_id',
        'return_date',
        'status',
        'processed_by',
    ];

    protected $casts = [
        'return_date' => 'datetime',
    ];

    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(ReturnDetail::class, 'return_id');
    }
}
