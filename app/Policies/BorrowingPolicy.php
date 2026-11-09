<?php

namespace App\Policies;

use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BorrowingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'supervisor';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Borrowing $borrowing): bool
    {
        return $user->role === 'admin' || $user->role === 'supervisor'; // Since borrower_id is gone, allow supervisors to view
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'supervisor';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Borrowing $borrowing): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Borrowing $borrowing): bool
    {
        return $user->role === 'admin';
    }
    
    /**
     * Determine whether the user can approve the borrowing.
     */
    public function approve(User $user, Borrowing $borrowing): bool
    {
        return $user->role === 'admin';
    }
}
