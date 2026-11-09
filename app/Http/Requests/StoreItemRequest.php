<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Handled by Policy
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.type' => 'required|in:consumable,returnable',
            'items.*.stock' => 'required|integer|min:0',
            'items.*.condition' => 'required|in:good,defect,damage',
        ];
    }
}
