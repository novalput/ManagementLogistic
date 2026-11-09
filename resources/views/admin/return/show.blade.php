@extends('layouts.admin')

@section('title', 'Return Details')

@section('header')
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Returning Page</h1>
        <h2 class="text-sm md:text-base text-gray-600 font-medium mt-1">Create and authorize a new item borrowing request for staffs</h2>
    </div>
@endsection

@section('content')
    <div class="flex flex-col gap-4 h-full" x-data>
        <div class="flex justify-end items-center gap-2 flex-shrink-0">
            <form method="POST" action="{{ route('return.verify') }}" class="relative">
                @csrf
                <input type="text" name="borrowing_id" value="{{ $borrowingId }}" 
                       placeholder="Input Borrowing ID"
                       class="w-64 px-4 py-2 pl-10 rounded-xl border-gray-200 focus:border-[#2B8AE2] focus:ring-[#2B8AE2] bg-white/50 backdrop-blur-sm transition-all text-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </form>
            <a href="{{ route('reports') }}" class="px-4 py-2 rounded-xl border border-[#2B8AE2] bg-white/60 text-[#2B8AE2] flex items-center gap-2 hover:bg-white/80 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
        </div>

        <div class="flex flex-col gap-4 lg:grid lg:grid-cols-[4fr_8fr] lg:grid-rows-[auto_1fr] lg:gap-6 lg:h-[calc(100vh-200px)] lg:min-h-[400px] flex-1">
            <!-- Left Column -->
            <div class="flex flex-col gap-4 lg:gap-6 lg:row-span-2 justify-center">
                <!-- Container 1: Information -->
                <x-card class="space-y-4 p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#2B8AE2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Information</h3>
                        </div>
                        @php
                            $overdue = $data['expected_return'] && now()->toDateString() > $data['expected_return'];
                        @endphp
                        @if($overdue)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#2B8AE2] text-white text-xs font-medium">
                                <span class="w-2 h-2 rounded-full bg-red-400 animate-pulse"></span>
                                Overdue
                            </span>
                        @endif
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center gap-3">
                            <span class="text-gray-600 w-32">Name</span>
                            <span class="px-3 py-1.5 rounded-full bg-white/80 font-medium text-gray-800">{{ $data['borrower'] }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-gray-600 w-32">Division</span>
                            <span class="px-3 py-1.5 rounded-full bg-white/80 font-medium text-gray-800">{{ $data['division'] }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-gray-600 w-32">Borrowed at</span>
                            <span class="px-3 py-1.5 rounded-full bg-white/80 font-medium text-gray-800">{{ \Carbon\Carbon::parse($data['borrowed_at'])->format('d M Y') }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-gray-600 w-32">Expected Return</span>
                            <span class="px-3 py-1.5 rounded-full bg-white/80 font-medium text-gray-800">{{ $data['expected_return'] ? \Carbon\Carbon::parse($data['expected_return'])->format('d M Y') : 'N/A' }}</span>
                        </div>
                    </div>
                </x-card>

                <!-- Container 2: Item Assessment -->
                <x-card class="p-5 flex flex-col min-h-0">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-[#2B8AE2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Item Assesment</h3>
                    </div>
                    <div class="text-sm text-gray-600 mb-2 font-medium">Notes</div>
                    <form action="{{ route('return.notes', $borrowingId) }}" method="POST">
                        @csrf
                        <textarea name="notes" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#2B8AE2] focus:ring-[#2B8AE2] bg-white/80 flex-1 resize-none min-h-[80px] text-sm" placeholder="Describe condition of items, reason for delay, etc.">{{ $borrowing->notes ?? '' }}</textarea>
                        <div class="mt-4">
                            <x-button.primary type="submit" class="w-full justify-center">Add Notes</x-button.primary>
                        </div>
                    </form>
                </x-card>
            </div>

            <!-- Right Column: Items Borrowed -->
            <x-card class="p-5 flex flex-col lg:row-span-2 min-h-[400px] lg:min-h-0">
                <div class="flex items-center justify-between mb-4 flex-shrink-0">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#2B8AE2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Items Borrowed</h3>
                    </div>
                    <span class="px-3 py-1.5 rounded-full bg-white/80 text-sm font-medium border border-gray-200">{{ count($data['items']) }} Items</span>
                </div>
                <div class="flex-1 overflow-y-auto rounded-xl min-h-0 bg-white/30 no-scrollbar">
                    <table class="min-w-full text-sm">
                        <thead class="bg-white/80 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Item Name</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Item Code</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Item Type</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Quantity</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Condition</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $isConsumableBase = count($data['items']) > 0 && $data['items'][0]['type'] === 'consumable'; @endphp
                            @foreach($data['items'] as $item)
                                <tr class="border-t border-gray-100 bg-white/30 odd:bg-white/50">
                                    <td class="px-4 py-3 text-gray-800">{{ $item['name'] }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $item['barcode'] }}</td>
                                    <td class="px-4 py-3 text-gray-600 capitalize">
                                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $item['type'] === 'consumable' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $item['type'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $item['quantity'] }}</td>
                                    <td class="px-4 py-3">
                                        @if($item['type'] === 'returnable')
                                            <input type="hidden" name="items[{{ $item['detail_id'] }}][detail_id]" value="{{ $item['detail_id'] }}" form="confirmReturnForm">
                                            <input type="hidden" name="items[{{ $item['detail_id'] }}][condition_in]" value="{{ $item['condition'] }}" form="confirmReturnForm">
                                            <select class="w-full rounded-md border-gray-200 bg-gray-100 text-gray-500 text-sm py-1.5 cursor-not-allowed" disabled>
                                                <option value="good" {{ strtolower($item['condition']) === 'good' ? 'selected' : '' }}>Good</option>
                                                <option value="defect" {{ strtolower($item['condition']) === 'defect' ? 'selected' : '' }}>Defect</option>
                                                <option value="damage" {{ strtolower($item['condition']) === 'damage' ? 'selected' : '' }}>Damage</option>
                                            </select>
                                        @else
                                            @if(strtolower($item['condition']) === 'defect')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-yellow-50 border border-yellow-200">
                                                    <span class="w-2 h-2 rounded-full bg-yellow-400"></span>
                                                    <span class="text-yellow-700 font-medium capitalize">{{ $item['condition'] }}</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-50 border border-green-200">
                                                    <span class="w-2 h-2 rounded-full bg-green-400"></span>
                                                    <span class="text-green-700 font-medium capitalize">{{ $item['condition'] }}</span>
                                                </span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if(collect($data['items'])->where('condition','Defect')->count() > 0)
                    <div class="mt-3 space-y-2 text-sm flex-shrink-0">
                        @foreach($data['items'] as $item)
                            @if($item['condition'] === 'Defect')
                                <p class="flex items-center gap-2 text-[#2B8AE2]">
                                    <svg class="w-4 h-4 text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Note: Defects Detected on <span class="font-semibold underline">{{ $item['name'] }}</span>. Please fill in the Item Assesment.
                                </p>
                            @endif
                        @endforeach
                    </div>
                @endif
                
                @if($borrowing->status !== 'returned')
                <div class="mt-4 flex justify-end flex-shrink-0">
                    @if(isset($isConsumableBase) && $isConsumableBase)
                        <span class="px-6 py-2.5 rounded-xl bg-purple-50 text-purple-600 font-medium border border-purple-200">Consumable items - No Return Required</span>
                    @else
                        @can('update', $borrowing)
                            <form id="confirmReturnForm" action="{{ route('return.confirm') }}" method="POST">
                                @csrf
                                <input type="hidden" name="borrowing_id" value="{{ $borrowingId }}">
                                <button type="button" id="btnConfirmReturn" class="px-6 py-2.5 rounded-xl bg-[#E2852E] text-white font-medium hover:bg-[#c9721f] transition-colors">Confirm Return</button>
                            </form>
                        @else
                            <button type="button" @click="$dispatch('open-modal', 'unauthorized-access')" class="px-6 py-2.5 rounded-xl bg-[#E2852E] text-white font-medium hover:bg-[#c9721f] transition-colors">Confirm Return</button>
                        @endcan
                    @endif
                </div>
                @else
                <div class="mt-4 flex justify-end flex-shrink-0">
                    <span class="px-6 py-2.5 rounded-xl bg-gray-100 text-gray-500 font-medium cursor-not-allowed border border-gray-200">Already Returned</span>
                </div>
                @endif
            </x-card>
        </div>

        <!-- Unauthorized Access Modal -->
        <x-modal.unauthorized />
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('error'))
            Swal.fire({
                title: 'Error!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonColor: '#DC2626',
                confirmButtonText: 'OK',
            });
        @endif

        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#2B8AE2',
                confirmButtonText: 'OK',
            });
        @endif

        document.getElementById('btnConfirmReturn')?.addEventListener('click', function() {
            Swal.fire({
                html: `
                    <div class="flex flex-col items-center py-4 px-2">
                        <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <h3 class="text-[#1F2937] text-xl font-medium mb-1">Confirmation</h3>
                        <p class="text-[#4B5563] text-base mb-2">Do you want to confirm this return?</p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Confirm',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'rounded-[32px] p-0 bg-white/90 backdrop-blur-xl shadow-2xl border border-white/50',
                    confirmButton: 'bg-[#DC2626] hover:bg-[#B91C1C] text-white px-8 py-3 rounded-2xl text-base font-semibold shadow-lg shadow-red-200 transition-all transform hover:scale-[1.02] border-0 mx-2',
                    cancelButton: 'bg-white hover:bg-gray-50 text-[#374151] px-8 py-3 rounded-2xl text-base font-semibold shadow-[0_2px_8px_rgba(0,0,0,0.08)] border border-gray-100 transition-all transform hover:scale-[1.02] mx-2',
                    actions: 'mb-6 gap-4 w-full flex justify-center',
                    htmlContainer: '!m-0 !p-0',
                    container: '!z-[1050]'
                },
                buttonsStyling: false,
                reverseButtons: true,
                width: '420px',
                padding: '24px',
                backdrop: `rgba(86, 73, 73, 0.4)`,
                didOpen: () => {
                    const container = document.getElementById('app-container');
                    if (container) {
                        container.style.transition = 'filter 0.3s ease-out';
                        container.style.filter = 'blur(8px)';
                    }
                },
                willClose: () => {
                    const container = document.getElementById('app-container');
                    if (container) {
                        container.style.filter = 'none';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('confirmReturnForm').submit();
                }
            })
        });
    </script>
@endsection
