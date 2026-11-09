<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\ItemUnit;

class ItemUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = Item::all();

        foreach ($items as $item) {
            // Check if units already exist to prevent duplicate seeding
            if ($item->units()->exists()) {
                continue;
            }

            // Create units based on current stock
            // Assuming current 'stock' column represents 'good' condition items
            for ($i = 1; $i <= $item->stock; $i++) {
                ItemUnit::create([
                    'item_id' => $item->id,
                    'serial_number' => $item->item_code . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'status' => 'available',
                    'condition' => 'good',
                ]);
            }
        }
    }
}
