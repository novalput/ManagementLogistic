<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\BorrowingDetail;
use App\Models\StockLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Check user role and redirect to appropriate dashboard
        if (auth()->user()->role === 'supervisor') {
            return $this->supervisorIndex();
        }

        // Calculate Stats
        $available_returnable = Item::where('type', 'returnable')->where('condition', 'good')->sum('stock');
        $available_consumable = Item::where('type', 'consumable')->where('condition', 'good')->sum('stock');
        
        // Count borrowed items (quantity sum from details where borrowing status is 'borrowed')
        $borrowed = BorrowingDetail::whereHas('borrowing', function ($query) {
            $query->where('status', 'borrowed');
        })->sum('quantity');

        $total = Item::sum('stock');

        $stats = [
            'total' => $total,
            'available_returnable' => $available_returnable,
            'available_consumable' => $available_consumable,
            'borrowed' => $borrowed,
        ];

        // Recent Activity
        // Fetch latest stock logs to show both borrows and returns
        $recentActivities = StockLog::with(['item'])
            ->orderBy('id', 'desc')
            ->take(5)
            ->get()
            ->map(function ($log) {
                // Determine action text
                $action = match($log->action) {
                    'borrow' => 'was borrowed',
                    'return' => 'was returned',
                    'adjustment' => 'stock was adjusted',
                    'add' => 'was added to stock',
                    default => 'was updated'
                };
                
                $dateObj = $log->created_at;
                $date = $dateObj->isToday() ? 'today' : $dateObj->diffForHumans();
                
                return (object) [
                    'item_name' => $log->item->item_name ?? 'Unknown Item',
                    'action' => $action,
                    'date' => $date
                ];
            });

        return view('admin.dashboard', compact('stats', 'recentActivities'));
    }

    public function supervisorIndex()
    {
        // Calculate Stats (same as admin)
        $available_returnable = Item::where('type', 'returnable')->where('condition', 'good')->sum('stock');
        $available_consumable = Item::where('type', 'consumable')->where('condition', 'good')->sum('stock');
        
        // Count borrowed items
        $borrowed = BorrowingDetail::whereHas('borrowing', function ($query) {
            $query->where('status', 'borrowed');
        })->sum('quantity');

        $total = Item::sum('stock');

        $stats = [
            'total' => $total,
            'available_returnable' => $available_returnable,
            'available_consumable' => $available_consumable,
            'borrowed' => $borrowed,
        ];

        // Recent Activity
        $recentActivities = StockLog::with(['item'])
            ->orderBy('id', 'desc')
            ->take(5)
            ->get()
            ->map(function ($log) {
                $action = match($log->action) {
                    'borrow' => 'was borrowed',
                    'return' => 'was returned',
                    'adjustment' => 'stock was adjusted',
                    'add' => 'was added to stock',
                    default => 'was updated'
                };
                
                $dateObj = $log->created_at;
                $date = $dateObj->isToday() ? 'today' : $dateObj->diffForHumans();
                
                return (object) [
                    'item_name' => $log->item->item_name ?? 'Unknown Item',
                    'action' => $action,
                    'date' => $date
                ];
            });

        return view('supervisor.dashboard', compact('stats', 'recentActivities'));
    }
}
