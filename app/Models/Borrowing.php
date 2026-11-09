<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Borrowing extends Model
{
    /** @use HasFactory<\Database\Factories\BorrowingFactory> */
    use HasFactory;

    protected $fillable = [
        'borrowing_code',
        'borrower_id',
        'borrower_name',
        'borrower_division',
        'borrow_date',
        'due_date',
        'purpose',
        'notes',
        'status',
        'requested_at',
        'authorized_by',
        'authorized_at',
    ];

    protected $casts = [
        'borrow_date' => 'datetime',
        'due_date' => 'datetime',
        'requested_at' => 'datetime',
        'authorized_at' => 'datetime',
    ];

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function authorizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(BorrowingDetail::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(ReturnTransaction::class, 'borrowing_id');
    }
}
