<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReturnRequest;
use App\Models\Borrowing;
use App\Models\Item;
use App\Models\ReturnTransaction;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReturnController extends Controller
{
    public function index()
    {
        return view('admin.return.index');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'borrowing_id' => ['required', 'string'],
        ]);

        $code = trim($request->input('borrowing_id'));
        
        // Find by code or ID
        $borrowing = Borrowing::where('borrowing_code', $code)
            ->orWhere('id', $code)
            ->first();

        if ($borrowing) {
            // Use ID for route parameter to be safe/standard, or code if preferred. 
            // The show method expects 'borrowingId'.
            return redirect()->route('return.show', ['borrowingId' => $borrowing->borrowing_code]); // Pass code for URL prettiness
        }

        return back()->withInput()->withErrors(['borrowing_id' => 'Borrowing ID not found']);
    }

    public function show(string $borrowingId)
    {
        $borrowingId = trim($borrowingId);
        
        $borrowing = Borrowing::where('borrowing_code', $borrowingId)
            ->orWhere('id', $borrowingId)
            ->with(['details.item'])
            ->first();

        if (!$borrowing) {
            return redirect()->route('return')->withErrors(['borrowing_id' => 'Borrowing ID not found']);
        }
        
        // Map to view data structure
        // We need to pass necessary info for the view and for the form (hidden inputs)
        $data = [
            'borrower' => $borrowing->borrower_name,
            'division' => $borrowing->borrower_division,
            'borrowed_at' => $borrowing->borrow_date->format('Y-m-d'),
            'expected_return' => $borrowing->due_date ? $borrowing->due_date->format('Y-m-d') : null,
            'items' => $borrowing->details->map(function($detail) {
                return [
                    'detail_id' => $detail->id,
                    'id' => $detail->item->id,
                    'name' => $detail->item->item_name,
                    'barcode' => $detail->item->item_code,
                    'condition' => $detail->item->condition, // Current item condition
                    'quantity' => $detail->quantity, // Borrowed quantity
                    'type' => $detail->item->type, // Item type mapping
                ];
            })
        ];

        return view('admin.return.show', [
            'borrowingId' => $borrowing->borrowing_code, // Keep using code for display
            'data' => $data,
            'borrowing' => $borrowing // Pass model if needed
        ]);
    }

    public function updateNotes(Request $request, string $borrowingId)
    {
        $borrowingId = trim($borrowingId);
        
        $borrowing = Borrowing::where('borrowing_code', $borrowingId)
            ->orWhere('id', $borrowingId)
            ->firstOrFail();

        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $borrowing->update([
            'notes' => $request->input('notes')
        ]);

        return back()->with('success', 'Notes updated successfully');
    }

    public function confirm(Request $request)
    {
        // We use Request here because we might handle "Return All" logic without explicit items array
        // But to be safe and use our Request class validation if items ARE sent:
        // Let's manually validate or use the StoreReturnRequest if we update the view.
        // For now, I'll implement logic to handle both or just "Return All" based on borrowing_id.
        
        $request->validate([
            'borrowing_id' => 'required',
        ]);

        $borrowingId = $request->input('borrowing_id');
        $borrowing = Borrowing::where('borrowing_code', $borrowingId)
            ->orWhere('id', $borrowingId)
            ->with(['details.item'])
            ->firstOrFail();

        if ($borrowing->status === 'returned') {
            return redirect()->route('reports')->with('error', 'This borrowing has already been returned.');
        }

        try {
            DB::transaction(function () use ($borrowing, $request) {
                // 1. Create Return Transaction
                $returnTransaction = ReturnTransaction::create([
                    'borrowing_id' => $borrowing->id,
                    'return_date' => now(),
                    'status' => 'returned', // Or checked against due date?
                    'processed_by' => auth()->id(),
                ]);

                // 2. Process Items
                // Assuming full return for now as per UI "Confirm Return" button
                foreach ($borrowing->details as $detail) {
                    $conditionIn = $detail->item->condition;
                    $targetItem = $detail->item;

                    if ($detail->item->type === 'returnable') {
                        $inputItems = $request->input('items', []);
                        if (isset($inputItems[$detail->id]['condition_in'])) {
                            $conditionIn = strtolower($inputItems[$detail->id]['condition_in']);
                        }

                        // Determine the target row for stock increment
                        if (strtolower($conditionIn) !== strtolower($detail->item->condition)) {
                            $targetItem = Item::where('item_code', $detail->item->item_code)
                                              ->where('condition', $conditionIn)
                                              ->first();
                            
                            if (!$targetItem) {
                                $targetItem = $detail->item->replicate();
                                $targetItem->condition = $conditionIn;
                                $targetItem->stock = 0;
                                $targetItem->save();
                            }
                        }

                        // Restore Stock in item_units table
                        $itemForLock = Item::lockForUpdate()->find($targetItem->id);
                        
                        // We need to find the specific units that were borrowed
                        $borrowedUnits = $detail->item->units()
                            ->where('status', 'borrowed')
                            ->limit($detail->quantity)
                            ->lockForUpdate()
                            ->get();

                        foreach ($borrowedUnits as $unit) {
                            $unit->update([
                                'status' => 'available',
                                'item_id' => $targetItem->id
                            ]);
                        }

                        // Restore legacy stock column
                        $itemForLock->increment('stock', $detail->quantity);
                        
                        // Log
                        StockLog::create([
                            'item_id' => $targetItem->id,
                            'user_id' => auth()->id(),
                            'quantity_change' => $detail->quantity,
                            'action' => 'return',
                            'description' => "Returned borrowing {$borrowing->borrowing_code} (condition: {$conditionIn})",
                            'meta_data' => [
                                'return_id' => $returnTransaction->id, 
                                'borrowing_id' => $borrowing->id,
                                'unit_ids' => $borrowedUnits->pluck('id')
                            ],
                        ]);
                    }

                    // Create Return Detail
                    $returnTransaction->details()->create([
                        'item_id' => $targetItem->id,
                        'quantity' => $detail->quantity,
                        'condition_in' => $conditionIn,
                    ]);
                }

                // 3. Update Borrowing Status
                $status = ($borrowing->due_date && now()->gt($borrowing->due_date)) ? 'overdue' : 'returned'; // Actually if returned, it's returned. Overdue is state before return.
                // If returned late, it is still "returned" but maybe flagged?
                // The schema has status: pending, borrowed, returned, overdue.
                // Usually 'returned' is the final state. 'overdue' is active state.
                $borrowing->update(['status' => 'returned']);
                
                // Flash data for report (legacy support for demo view)
                session()->flash('new_return', [
                    'borrow_id' => $borrowing->borrowing_code,
                    'borrower' => $borrowing->borrower_name,
                    'loan_period' => $borrowing->borrow_date->format('d M Y') . ' - ' . ($borrowing->due_date ? $borrowing->due_date->format('d M Y') : 'N/A'),
                ]);
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Error processing return: ' . $e->getMessage());
        }

        return redirect()->route('reports')->with('success', 'Items successfully returned!');
    }
}
