<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\BorrowingDetail;
use App\Models\StockLog;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Statistik dashboard — sama dengan web (DashboardController).
     * total = sum(stock) good + defect/damage; borrowed = sum(quantity) dari detail yang status borrowed.
     */
    public function index(): JsonResponse
    {
        $available = (int) Item::where('condition', 'good')->sum('stock');
        $defective = (int) Item::whereIn('condition', ['defect', 'damage'])->sum('stock');
        $borrowed = (int) BorrowingDetail::whereHas('borrowing', function ($query) {
            $query->where('status', 'borrowed');
        })->sum('quantity');

        $total = $available + $defective;

        $recentActivities = $this->getRecentActivities();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_items' => $total,
                'available_items' => $available,
                'borrowed_items' => $borrowed,
                'defect_items' => $defective,
                'recent_activities' => $recentActivities,
            ],
        ]);
    }

    /**
     * Recent activity dari StockLog — format sesuai yang dipakai Flutter.
     */
    protected function getRecentActivities(): array
    {
        $logs = StockLog::with('item')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        return $logs->map(function ($log) {
            $action = match ($log->action) {
                'borrow' => 'was borrowed',
                'return' => 'was returned',
                'adjustment' => 'stock was adjusted',
                'add' => 'was added to stock',
                default => 'was updated',
            };
            $date = $log->created_at->isToday()
                ? 'today'
                : $log->created_at->diffForHumans();

            return [
                'title' => ($log->item->item_name ?? 'Unknown Item') . ' ' . $action,
                'description' => $date,
                'created_at' => $log->created_at->toIso8601String(),
                'type' => $log->action,
            ];
        })->values()->all();
    }
}

