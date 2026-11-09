<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Borrowing;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        $query = Borrowing::with(['borrower', 'details.item'])->orderBy('created_at', 'desc');

        if ($status === 'overdue') {
            $query->where('status', 'borrowed')
                  ->whereDate('due_date', '<', now()->startOfDay());
        } elseif ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('borrowing_code', 'like', "%{$search}%")
                  ->orWhere('borrower_name', 'like', "%{$search}%");
            });
        }

        $reports = $query->paginate(10)->withQueryString();

        return view('admin.reports.index', compact('reports', 'status', 'search'));
    }

    public function exportPdf(Request $request)
    {
        $monthYear = $request->get('month', now()->format('Y-m'));
        
        $query = Borrowing::with(['borrower', 'details.item', 'returns'])
            ->whereYear('borrow_date', substr($monthYear, 0, 4))
            ->whereMonth('borrow_date', substr($monthYear, 5, 2))
            ->orderBy('borrow_date', 'asc');

        $reports = $query->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.pdf', compact('reports', 'monthYear'));
        
        // Export portrait A4
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('monthly-borrowing-report-'.$monthYear.'.pdf');
    }
}