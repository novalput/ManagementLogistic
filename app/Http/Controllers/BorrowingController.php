<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBorrowingRequest;
use App\Models\Borrowing;
use App\Models\Item;
use App\Models\User;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class BorrowingController extends Controller
{
    /**
     * Display the borrowing page.
     */
    public function create(string $type)
    {
        Gate::authorize('create', Borrowing::class);

        $users = User::where('role', 'supervisor')->get();
        $userDivs = User::whereNotNull('division')
            ->distinct()
            ->pluck('division');
            
        $borrowDivs = Borrowing::whereNotNull('borrower_division')
            ->distinct()
            ->pluck('borrower_division');

        $divisions = $userDivs->concat($borrowDivs)
            ->filter()
            ->unique()
            ->values();
        
        return view('admin.borrow', compact('users', 'divisions', 'type'));
    }

    public function scanItem(Request $request)
    {
        $request->validate([
            'item_code' => 'required|string',
        ]);

        // Get all items with this code that are not damaged
        $items = Item::where('item_code', $request->item_code)
                    ->where('condition', '!=', 'damage')
                    ->where('stock', '>', 0)
                    ->get();

        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found or out of stock / damaged'
            ], 200);
        }

        return response()->json([
            'success' => true,
            'items' => $items->map(function($item) {
                return [
                    'id' => $item->id,
                    'item_code' => $item->item_code,
                    'item_name' => $item->item_name,
                    'type' => $item->type,
                    'condition' => $item->condition,
                    'stock' => $item->stock,
                ];
            })
        ], 200);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Borrowing::class);
        $borrowings = Borrowing::with(['details.item', 'authorizer'])->get();
        return response()->json($borrowings);
    }

    private function processApproval(Borrowing $borrowing)
    {
        // Lock items to prevent race conditions
        // Sort by item_id to prevent deadlocks if multiple approvals happen
        $details = $borrowing->details->sortBy('item_id');

        foreach ($details as $detail) {
            $item = Item::lockForUpdate()->find($detail->item_id);
            
            // Validate stock directly from items.stock field
            if ($item->stock < $detail->quantity) {
                throw new \Exception("Insufficient stock for item: {$item->item_name}");
            }
            
            // Decrement stock only after admin approval
            $item->decrement('stock', $detail->quantity);
            
            // Try to update units if they exist, but don't fail if they don't
            // This handles hybrid state where some items might not have units generated yet
            $availableUnits = $item->units()
                ->where('status', 'available')
                ->where('condition', 'good')
                ->limit($detail->quantity)
                ->get();

            foreach ($availableUnits as $unit) {
                $unit->update(['status' => 'borrowed']);
            }
            
            // Log
            StockLog::create([
                'item_id' => $item->id,
                'user_id' => auth()->id(),
                'quantity_change' => -((int)$detail->quantity),
                'action' => 'borrow',
                'description' => "Approved borrowing {$borrowing->borrowing_code}",
                'meta_data' => ['borrowing_id' => $borrowing->id, 'unit_ids' => $availableUnits->pluck('id')],
            ]);
        }

        $borrowing->update([
            'status' => 'borrowed',
            'authorized_by' => auth()->id(),
            'authorized_at' => now(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBorrowingRequest $request, string $type)
    {
        Gate::authorize('create', Borrowing::class);

        $validated = $request->validated();

        try {
            return DB::transaction(function () use ($validated, $request, $type) {
                $isAllConsumable = true;
                // Check if items are available (check directly against stock column)
                foreach ($validated['items'] as $itemData) {
                    $item = Item::find($itemData['item_id']);
                    if ($item->stock < $itemData['quantity']) {
                        throw new \Exception("Insufficient stock for item: {$item->item_name}");
                    }
                    if ($item->type !== $type) {
                        throw new \Exception("Item {$item->item_name} is not a {$type} item.");
                    }
                    if ($item->type !== 'consumable') {
                        $isAllConsumable = false;
                    }
                }
                
                if ($type === 'returnable' && empty($validated['due_date'])) {
                    throw new \Exception("Return Date is required for returnable items.");
                }

                // Generate RDM-YYYY-XXXX format
                $year = now()->year;
                $prefix = "RDM-{$year}-";
                $lastBorrowing = Borrowing::where('borrowing_code', 'like', "{$prefix}%")
                    ->orderBy('id', 'desc')
                    ->first();
                
                $nextSequence = 1;
                if ($lastBorrowing) {
                    $parts = explode('-', $lastBorrowing->borrowing_code);
                    if (count($parts) === 3 && is_numeric($parts[2])) {
                        $nextSequence = intval($parts[2]) + 1;
                    }
                }
                $borrowingCode = $prefix . str_pad($nextSequence, 5, '0', STR_PAD_LEFT);

                $borrowing = Borrowing::create([
                    'borrowing_code' => $borrowingCode,
                    'borrower_id' => auth()->id(),
                    'borrower_name' => $validated['borrower_name'],
                    'borrower_division' => $validated['borrower_division'],
                    'borrow_date' => $validated['borrow_date'],
                    'due_date' => $validated['due_date'] ?? null,
                    'purpose' => $validated['purpose'] ?? null,
                    'status' => 'pending',
                    'requested_at' => now(),
                ]);

                foreach ($validated['items'] as $itemData) {
                    $itemModel = Item::find($itemData['item_id']);
                    $borrowing->details()->create([
                        'item_id' => $itemData['item_id'],
                        'quantity' => $itemData['quantity'],
                        'condition_out' => $itemModel->condition,
                    ]);
                }
                
                // If user is admin, auto-approve immediately
                if (auth()->user()->role === 'admin') {
                    $this->processApproval($borrowing);
                }

                if ($request->wantsJson()) {
                    return response()->json($borrowing->load('details'), 201);
                }

                return redirect()->route('borrow.create', ['type' => $type])->with('success', 'Borrowing request created successfully!');
            });
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Borrowing $borrowing)
    {
        Gate::authorize('view', $borrowing);
        return response()->json($borrowing->load(['details.item', 'authorizer']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Borrowing $borrowing)
    {
        Gate::authorize('update', $borrowing);

        $validated = $request->validate([
            'status' => 'required|in:pending,borrowed,returned,overdue',
            'authorized_by' => 'nullable|exists:users,id',
            // Add other fields if needed
        ]);

        // Prevent status change to 'borrowed' via update if it was pending
        // 'approve' method must be used for that transition to ensure stock deduction
        if ($borrowing->status === 'pending' && $validated['status'] === 'borrowed') {
             return response()->json(['error' => 'Please use approve endpoint to authorize borrowing.'], 400);
        }

        $borrowing->update($validated);

        return response()->json($borrowing);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Borrowing $borrowing)
    {
        Gate::authorize('delete', $borrowing);
        
        $borrowing->delete();
        if (request()->wantsJson()) {
            return response()->json(null, 204);
        }
        return redirect()->back()->with('status', 'borrowing-deleted');
    }

    public function approve(Request $request, Borrowing $borrowing)
    {
        Gate::authorize('approve', $borrowing);

        if ($borrowing->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending requests can be approved.');
        }

        try {
            DB::transaction(function () use ($borrowing) {
                $this->processApproval($borrowing);
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('status', 'Request approved successfully.');
    }

    public function reject(Request $request, Borrowing $borrowing)
    {
        Gate::authorize('delete', $borrowing); // Using delete permission or custom reject
        
        $borrowing->delete();
        return redirect()->back()->with('status', 'Request rejected and removed.');
    }
}
