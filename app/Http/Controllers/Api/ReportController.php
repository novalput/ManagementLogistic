<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        // Logic query sama persis dengan Web ReportController
        $query = Borrowing::orderBy('created_at', 'desc');

        if ($status === 'overdue') {
            // Overdue = masih borrowed di DB tapi due_date sudah lewat
            $query->where('status', 'borrowed')
                  ->whereDate('due_date', '<', now()->startOfDay());
        } elseif ($status !== 'all') {
            $query->where('status', strtolower($status));
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('borrowing_code', 'like', "%{$search}%")
                  ->orWhere('borrower_name', 'like', "%{$search}%");
            });
        }

        // Gunakan pagination
        $borrowings = $query->paginate(10);
        
        // Transform data agar format JSON sesuai ekspektasi Flutter
        $reports = $borrowings->through(function ($borrowing) {
            // Logic status display sama dengan Web
            $isOverdue = $borrowing->status === 'borrowed' 
                && $borrowing->due_date->format('Y-m-d') < now()->format('Y-m-d');
            $statusDisplay = $isOverdue ? 'Overdue' : ucfirst($borrowing->status);

            return [
                'id' => $borrowing->id,
                'borrow_id' => $borrowing->borrowing_code, // Flutter mengharapkan key ini
                'borrower_name' => $borrowing->borrower_name, // Flutter mengharapkan key ini
                'division' => $borrowing->borrower_division,
                'borrow_date' => $borrowing->borrow_date->format('d M Y'),
                'return_date' => $borrowing->due_date->format('d M Y'),
                'status' => $statusDisplay,
            ];
        });

        return response()->json($reports);
    }
}
