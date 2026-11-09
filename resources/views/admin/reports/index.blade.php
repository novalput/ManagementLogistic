@extends('layouts.admin')

@section('title', 'Reports')

@section('header')
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Reports</h1>
        <h2 class="text-sm md:text-base text-gray-600 font-medium mt-1">Borrowing reports.</h2>
    </div>
@endsection

@section('content')
    <div class="glass-card rounded-2xl md:rounded-[24px] p-4 md:p-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Reports List</h2>
            
            <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
                <form method="GET" action="{{ route('reports.exportPdf') }}" class="flex items-center gap-2 w-full md:w-auto">
                    <input type="month" name="month" value="{{ request('month', now()->format('Y-m')) }}" 
                        class="w-full md:w-auto px-4 py-2 rounded-xl border-gray-200 focus:border-[#2B8AE2] focus:ring-[#2B8AE2] bg-white/50 backdrop-blur-sm transition-all text-sm" required>
                    <button type="submit" class="px-4 py-2 bg-[#0D1A63] text-white rounded-xl hover:bg-[#1b2f8a] transition-colors text-sm font-medium whitespace-nowrap inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export PDF
                    </button>
                </form>

                <form method="GET" action="{{ route('reports') }}" class="relative w-full md:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search..."
                        class="w-full md:w-64 px-4 py-2 pl-10 rounded-xl border-gray-200 focus:border-[#2B8AE2] focus:ring-[#2B8AE2] bg-white/50 backdrop-blur-sm transition-all text-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>
            </div>
        </div>

        @php $status = request('status', 'all'); @endphp
        <div class="flex items-center justify-start md:justify-end gap-2 mb-4 overflow-x-auto pb-2 md:pb-0 no-scrollbar">
            <a href="{{ route('reports', ['status' => 'all'] + request()->except('page')) }}"
               class="px-4 py-1 whitespace-nowrap rounded-full border border-[#0D1A63] {{ $status === 'all' ? 'bg-[#0D1A63] text-white' : 'bg-white/60 text-[#0D1A63]' }}">
                All
            </a>
            <a href="{{ route('reports', ['status' => 'borrowed'] + request()->except('page')) }}"
               class="px-4 py-1 whitespace-nowrap rounded-full border border-[#0D1A63] {{ $status === 'borrowed' ? 'bg-[#0D1A63] text-white' : 'bg-white/60 text-[#0D1A63]' }}">
                Borrowed
            </a>
            <a href="{{ route('reports', ['status' => 'returned'] + request()->except('page')) }}"
               class="px-4 py-1 whitespace-nowrap rounded-full border border-[#0D1A63] {{ $status === 'returned' ? 'bg-[#0D1A63] text-white' : 'bg-white/60 text-[#0D1A63]' }}">
                Returned
            </a>
            <a href="{{ route('reports', ['status' => 'overdue'] + request()->except('page')) }}"
               class="px-4 py-1 whitespace-nowrap rounded-full border border-[#0D1A63] {{ $status === 'overdue' ? 'bg-[#0D1A63] text-white' : 'bg-white/60 text-[#0D1A63]' }}">
                Overdue
            </a>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-200/50 bg-white/50">
            <table class="min-w-full">
                <thead class="bg-white/90 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 whitespace-nowrap">No</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 whitespace-nowrap">Borrow ID</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 whitespace-nowrap">Borrower</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 whitespace-nowrap">Type</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 whitespace-nowrap">Loan Period</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 whitespace-nowrap">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 whitespace-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reports as $report)
                        <tr class="bg-white/70 hover:bg-white/90 transition-colors {{ isset($report->is_new) && $report->is_new ? 'bg-green-50/80' : '' }}">
                            <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800 whitespace-nowrap">{{ $report->borrowing_code }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $report->borrower_name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap capitalize">
                                @php $rowType = $report->details->first()?->item->type ?? 'N/A'; @endphp
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $rowType === 'consumable' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $rowType }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">
                                {{ $report->borrow_date->format('d M Y') }} - {{ $report->due_date ? $report->due_date->format('d M Y') : 'N/A' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $isOverdue = $report->status === 'borrowed' && $report->due_date && $report->due_date->isPast();
                                @endphp
                                @if($report->status === 'returned')
                                    <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs font-medium">Returned</span>
                                @elseif($isOverdue)
                                    <span class="px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-medium">Overdue</span>
                                @else
                                    <span class="px-2 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-medium">Borrowed</span>
                                @endif
                            </td>
                            @if (auth()->user()->role === 'admin')
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('return.show', ['borrowingId' => $report->id]) }}" class="text-[#0D1A63] hover:text-[#1b2f8a] font-medium text-sm">
                                    {{ $rowType === 'consumable' ? 'View' : 'Details' }}
                                </a>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center">
                                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-50 border border-gray-200">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 112 0v6m-2 0h2m-6 4h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-gray-600 text-sm">No records yet</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @if(session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    confirmButtonColor: '#2B8AE2',
                    confirmButtonText: 'OK',
                    background: 'rgba(255, 255, 255, 0.95)',
                    customClass: {
                        popup: 'rounded-[24px]',
                        confirmButton: 'rounded-xl px-6 py-2'
                    },
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        </script>
    @endif

        <div class="mt-4">
            {{ $reports->appends(request()->query())->links() }}
        </div>
    </div>
@endsection