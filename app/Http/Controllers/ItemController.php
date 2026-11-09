<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Item::class);

        $query = Item::orderBy('item_code', 'asc');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('item_code', 'like', "%{$search}%")
                  ->orWhere('item_name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
        }

        $items = $query->paginate(10);

        if ($request->is('api/*')) {
            return response()->json($items);
        }

        return view('items.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Item::class);
        return view('items.create');
    }

    public function store(StoreItemRequest $request)
    {
        Gate::authorize('create', Item::class);
        
        $validated = $request->validated();
        
        $createdCount = 0;
        $nameToCodeMap = [];

        foreach ($validated['items'] as $itemData) {
            $nameLower = strtolower(trim($itemData['item_name']));
            
            if (isset($nameToCodeMap[$nameLower])) {
                $itemCode = $nameToCodeMap[$nameLower];
            } else {
                $existingItem = \App\Models\Item::whereRaw('LOWER(TRIM(item_name)) = ?', [$nameLower])->first();
                if ($existingItem) {
                    $itemCode = $existingItem->item_code;
                } else {
                    $itemCode = $this->generateUniqueItemCode();
                }
                $nameToCodeMap[$nameLower] = $itemCode;
            }
            
            $itemData['item_code'] = $itemCode;

            $existingRow = \App\Models\Item::where('item_code', $itemCode)
                               ->where('condition', $itemData['condition'])
                               ->first();
            
            if ($existingRow) {
                $existingRow->stock += $itemData['stock'];
                $existingRow->save();
            } else {
                \App\Models\Item::create($itemData);
            }
            
            $createdCount++;
        }

        return redirect()->route('items.list')->with('success', $createdCount . ' Item(s) processed successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        Gate::authorize('view', $item);
        return view('items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        Gate::authorize('update', $item);
        return view('items.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemRequest $request, Item $item)
    {
        Gate::authorize('update', $item);
        
        $item->update($request->validated());

        return redirect()->route('items.list')->with('success', 'Item updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        Gate::authorize('delete', $item);
        
        $item->delete();
        return redirect()->route('items.list')->with('success', 'Item deleted successfully');
    }

    /**
     * Download the barcode for the specified item.
     */
    public function downloadBarcode(Item $item)
    {
        Gate::authorize('downloadBarcode', $item);

        // Use SVG for download as well since GD/Imagick are missing
        $generator = new BarcodeGeneratorSVG();
        $barcodeData = $generator->getBarcode($item->item_code, $generator::TYPE_CODE_128);

        return response($barcodeData)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="barcode-' . $item->item_code . '.svg"');
    }

    /**
     * Preview the barcode for the specified item (view in browser).
     */
    public function previewBarcode(Item $item)
    {
        Gate::authorize('view', $item);

        $generator = new BarcodeGeneratorSVG();
        $barcodeData = $generator->getBarcode($item->item_code, $generator::TYPE_CODE_128);

        return response($barcodeData)
            ->header('Content-Type', 'image/svg+xml');
    }

    /**
     * Generate barcode image with text.
     * @deprecated GD/Imagick not available in current environment. Use SVG renderer instead.
     */
    private function generateBarcodeWithText($code)
    {
        // This method requires GD or Imagick which are not enabled in the current PHP environment.
        // Keeping it for reference or future use if extensions are enabled.
        if (!extension_loaded('gd') && !extension_loaded('imagick')) {
             throw new \Exception('GD or Imagick extension is required for PNG generation.');
        }

        $generator = new BarcodeGeneratorPNG();
        $barcodeData = $generator->getBarcode($code, $generator::TYPE_CODE_128, 3, 50);
        
        $barcodeImage = imagecreatefromstring($barcodeData);
        $barcodeWidth = imagesx($barcodeImage);
        $barcodeHeight = imagesy($barcodeImage);
        
        $textPadding = 5; 
        $fontSize = 5; 
        $fontHeight = imagefontheight($fontSize);
        $fontWidth = imagefontwidth($fontSize);
        
        $totalHeight = $barcodeHeight + $textPadding + $fontHeight + 5;
        
        $finalImage = imagecreatetruecolor($barcodeWidth, $totalHeight);
        $white = imagecolorallocate($finalImage, 255, 255, 255);
        imagefilledrectangle($finalImage, 0, 0, $barcodeWidth, $totalHeight, $white);
        
        imagecopy($finalImage, $barcodeImage, 0, 0, 0, 0, $barcodeWidth, $barcodeHeight);
        
        $black = imagecolorallocate($finalImage, 0, 0, 0);
        $textWidth = strlen($code) * $fontWidth;
        $x = ($barcodeWidth - $textWidth) / 2;
        $y = $barcodeHeight + $textPadding;
        
        imagestring($finalImage, $fontSize, $x, $y, $code, $black);
        
        ob_start();
        imagepng($finalImage);
        $imageData = ob_get_clean();
        
        imagedestroy($barcodeImage);
        imagedestroy($finalImage);
        
        return $imageData;
    }

    /**
     * Generate a unique numeric item code.
     */
    private function generateUniqueItemCode()
    {
        $maxNumber = \App\Models\Item::where('item_code', 'LIKE', 'ITM%')
            ->get()
            ->map(function ($item) {
                return (int) substr($item->item_code, 3);
            })
            ->max();

        $newNumber = $maxNumber ? $maxNumber + 1 : 1;

        return 'ITM' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
