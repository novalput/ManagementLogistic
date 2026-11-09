<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index()
    {
        return response()->json(Item::orderBy('item_name')->get());
    }

    public function show($id)
    {
        $item = Item::find($id);
        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }
        return response()->json($item);
    }

    public function findByBarcode($code)
    {
        $item = Item::where('item_code', $code)->first();
        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }
        return response()->json($item);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => ['required', 'string', 'max:255'],
            'type'      => ['required', 'in:consumable,returnable'],
            'condition' => ['required', 'string', 'max:50'],
            'stock'     => ['required', 'integer', 'min:0'],
            'image'     => ['nullable', 'image', 'max:2048'],
        ]);

        // generate item_code jika Anda butuh kolom ini
        if (!isset($validated['item_code'])) {
            $validated['item_code'] = $this->generateUniqueItemCode();
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public');
            $validated['image_path'] = $path; // pastikan kolom ini ada di tabel
        }

        $item = Item::create($validated);

        return response()->json($item, 201);
    }

    private function generateUniqueItemCode()
    {
        do {
            $code = (string) random_int(10000000, 99999999) . (string) random_int(10000000, 99999999);
        } while (Item::where('item_code', $code)->exists());

        return $code;
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'item_name' => ['sometimes', 'required', 'string', 'max:255'],
            'type'      => ['sometimes', 'required', 'in:consumable,returnable'],
            'condition' => ['sometimes', 'required', 'string', 'max:50'],
            'stock'     => ['sometimes', 'required', 'integer', 'min:0'],
            'image'     => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public');
            $validated['image_path'] = $path;
        }

        $item->update($validated);

        return response()->json($item);
    }

    public function destroy(Item $item)
    {
        // Jika perlu, hapus file lama: Storage::disk('public')->delete($item->image_path);
        $item->delete();
        return response()->json(['message' => 'deleted']);
    }
}
