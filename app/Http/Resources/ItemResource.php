<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_code' => $this->item_code,
            'item_name' => $this->item_name,
            'category' => $this->category,
            'stock' => $this->stock,
            'condition' => $this->condition,
            'aggregated_stock' => $this->aggregated_stock,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
