<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Item;
use App\Models\ReturnDetail;
use App\Models\ReturnTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BorrowController extends Controller
{
    public function index(Request $request)
    {
        $query = Borrowing::with(['borrower', 'details.item', 'authorizer']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('user_id')) {
            $query->where('borrower_id', $request->user_id);
        }

        $borrowings = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $borrowings
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'borrower_name' => 'required|string|max:255',
            'division'      => 'required|string|max:255',
            'borrow_date'   => 'required|date',
            'return_date'   => 'required|date|after_or_equal:borrow_date',
            'purpose'       => 'nullable|string|max:1000',
            'item_codes'    => 'required|array',
            'item_codes.*'  => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $data = $validator->validated();

        // Aggregate qty per item_code (same code can appear multiple times = multiple units)
        $codeToQty = [];
        foreach ($data['item_codes'] as $code) {
            $code = trim((string) $code);
            if ($code === '') {
                continue;
            }
            $codeToQty[$code] = ($codeToQty[$code] ?? 0) + 1;
        }

        if (empty($codeToQty)) {
            return response()->json(['message' => 'Minimal satu item harus dipilih.'], 422);
        }

        $codes = array_keys($codeToQty);
        $itemsByCode = Item::whereIn('item_code', $codes)->get()->keyBy('item_code');
        $notFoundCodes = array_diff($codes, $itemsByCode->keys()->all());

        // All codes must exist in master data
        if (!empty($notFoundCodes)) {
            $codesList = implode(', ', array_map(fn ($c) => "'{$c}'", $notFoundCodes));
            return response()->json([
                'message' => "Item tidak ditemukan di master data: {$codesList}. Tambahkan item dengan Item Code tersebut di menu Items.",
            ], 422);
        }

        $itemIds = $itemsByCode->pluck('id')->unique()->values()->all();

        try {
            $result = DB::transaction(function () use ($data, $codeToQty, $itemsByCode, $itemIds) {
                // Lock item rows so stock is consistent (prevent race)
                $itemsLocked = Item::whereIn('id', $itemIds)->lockForUpdate()->get()->keyBy('id');

                // Validate stock: each item must have stock >= qty to borrow
                foreach ($codeToQty as $code => $qty) {
                    $item = $itemsByCode->get($code);
                    if (!$item) {
                        continue;
                    }
                    $locked = $itemsLocked->get($item->id);
                    $currentStock = $locked ? (int) $locked->stock : 0;
                    if ($currentStock < $qty) {
                        $name = $locked->item_name ?? $item->item_name ?? $code;
                        throw new \Exception(
                            "Stok tidak mencukupi untuk item \"{$name}\" (kode: {$code}). Tersedia: {$currentStock}, dibutuhkan: {$qty}."
                        );
                    }
                }

                $year = now()->format('Y');
                $last = Borrowing::where('borrowing_code', 'like', "BRW-{$year}-%")
                    ->orderByDesc('id')
                    ->value('borrowing_code');
                $seq = $last ? (int) substr($last, -4) + 1 : 1;
                $borrowingCode = sprintf('BRW-%s-%04d', $year, $seq);

                $borrowing = Borrowing::create([
                    'borrowing_code'    => $borrowingCode,
                    'borrower_id'       => auth()->id(),
                    'borrower_name'     => $data['borrower_name'],
                    'borrower_division' => $data['division'],
                    'borrow_date'       => $data['borrow_date'],
                    'due_date'          => $data['return_date'],
                    'purpose'           => $data['purpose'] ?? null,
                    'status'            => 'borrowed',
                    'requested_at'      => now(),
                    'authorized_by'     => auth()->id(),
                    'authorized_at'     => now(),
                ]);

                foreach ($codeToQty as $code => $qty) {
                    $item = $itemsByCode->get($code);
                    if (!$item || $qty < 1) {
                        continue;
                    }
                    $borrowing->details()->create([
                        'item_id'   => $item->id,
                        'quantity' => $qty,
                    ]);
                }

                foreach ($codeToQty as $code => $qty) {
                    $item = $itemsByCode->get($code);
                    if (!$item || $qty < 1) {
                        continue;
                    }
                    Item::where('id', $item->id)->decrement('stock', $qty);
                }

                return ['borrowing' => $borrowing, 'code' => $borrowingCode];
            });
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message'   => 'Peminjaman berhasil disimpan',
            'borrow_id' => $result['code'],
        ], 201);
    }

    /**
     * GET /borrowings/{id} — detail satu borrowing untuk layar Report Detail di Flutter.
     * Response: items (id = borrowing_detail.id), returned_item_ids, purpose, notes.
     */
    public function show(int $id)
    {
        $borrowing = Borrowing::with(['details.item'])->find($id);

        if (!$borrowing) {
            return response()->json(['message' => 'Peminjaman tidak ditemukan.'], 404);
        }

        $returnedQuantityColumn = \Schema::hasColumn('borrowing_details', 'returned_quantity');
        $items = $borrowing->details->map(function (BorrowingDetail $detail) use ($returnedQuantityColumn) {
            $item = $detail->item;
            $returned = $returnedQuantityColumn ? ($detail->returned_quantity >= $detail->quantity) : false;
            return [
                'id' => $detail->id,
                'item_id' => $item->id,
                'item_code' => $item->item_code,
                'item_name' => $item->item_name,
                'category' => $item->category ?? '',
                'condition' => $item->condition ?? 'good',
                'quantity' => $detail->quantity,
            ];
        })->values()->all();

        $returnedItemIds = $borrowing->details
            ->filter(function (BorrowingDetail $d) use ($returnedQuantityColumn) {
                return $returnedQuantityColumn && $d->returned_quantity >= $d->quantity;
            })
            ->pluck('id')
            ->values()
            ->all();

        return response()->json([
            'id' => $borrowing->id,
            'borrowing_code' => $borrowing->borrowing_code,
            'borrower_name' => $borrowing->borrower_name,
            'borrower_division' => $borrowing->borrower_division,
            'borrow_date' => $borrowing->borrow_date->format('Y-m-d'),
            'due_date' => $borrowing->due_date->format('Y-m-d'),
            'purpose' => $borrowing->purpose ?? '-',
            'notes' => $borrowing->notes ?? '-',
            'status' => $borrowing->status,
            'items' => $items,
            'returned_item_ids' => $returnedItemIds,
        ]);
    }

    /**
     * POST /borrowings/{id}/return — proses pengembalian (partial/full).
     * Body: { "items": [ { "item_id": <borrowing_detail_id>, "quantity": n, "condition": "good|damaged|lost" } ] }
     * Menambah kembali stok item dan menandai detail yang dikembalikan.
     */
    public function processReturn(Request $request, int $id)
    {
        if (!\Schema::hasColumn('borrowing_details', 'returned_quantity')) {
            return response()->json([
                'message' => 'Fitur return belum tersedia. Jalankan migration: add_returned_quantity_to_borrowing_details_table.',
            ], 503);
        }

        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.item_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.condition' => 'nullable|string|in:good,damaged,lost',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $borrowing = Borrowing::with(['details.item'])->find($id);

        if (!$borrowing) {
            return response()->json(['message' => 'Peminjaman tidak ditemukan.'], 404);
        }

        if ($borrowing->status === 'returned') {
            return response()->json(['message' => 'Peminjaman ini sudah dikembalikan seluruhnya.'], 422);
        }

        $payload = $validator->validated()['items'];
        $detailIds = array_column($payload, 'item_id');
        $detailsById = $borrowing->details->keyBy('id');

        foreach ($payload as $row) {
            $detailId = (int) $row['item_id'];
            $qty = (int) $row['quantity'];
            $detail = $detailsById->get($detailId);
            if (!$detail) {
                return response()->json([
                    'message' => "Detail peminjaman (id: {$detailId}) tidak termasuk dalam peminjaman ini.",
                ], 422);
            }
            $alreadyReturned = (int) ($detail->returned_quantity ?? 0);
            $remaining = $detail->quantity - $alreadyReturned;
            if ($qty > $remaining || $remaining <= 0) {
                return response()->json([
                    'message' => "Jumlah pengembalian untuk item \"{$detail->item->item_name}\" melebihi yang belum dikembalikan (sisa: {$remaining}).",
                ], 422);
            }
        }

        try {
            DB::transaction(function () use ($borrowing, $payload, $detailsById) {
                $returnTransaction = ReturnTransaction::create([
                    'borrowing_id' => $borrowing->id,
                    'return_date' => now(),
                    'status' => 'returned',
                    'processed_by' => auth()->id(),
                ]);

                foreach ($payload as $row) {
                    $detailId = (int) $row['item_id'];
                    $qty = (int) $row['quantity'];
                    $detail = $detailsById->get($detailId);
                    if (!$detail || $qty < 1) {
                        continue;
                    }

                    BorrowingDetail::where('id', $detail->id)->increment('returned_quantity', $qty);
                    Item::where('id', $detail->item_id)->increment('stock', $qty);
                    $returnTransaction->details()->create([
                        'item_id' => $detail->item_id,
                        'quantity' => $qty,
                    ]);
                }

                $borrowing->refresh();
                $allReturned = $borrowing->details->every(function (BorrowingDetail $d) {
                    return $d->returned_quantity >= $d->quantity;
                });
                if ($allReturned) {
                    $borrowing->update(['status' => 'returned']);
                }
            });
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Gagal memproses pengembalian: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'message' => 'Pengembalian berhasil dicatat. Stok item telah ditambah kembali.',
        ], 200);
    }
}
