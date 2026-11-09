<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBorrowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'borrower_name' => 'required|string|max:255',
            'borrower_division' => 'required|string|max:255',
            'borrow_date' => 'required|date|after_or_equal:today',
            'due_date' => 'nullable|date|after_or_equal:borrow_date',
            'purpose' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }
}
