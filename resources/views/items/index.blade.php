@extends('layouts.admin')

@section('title', 'Items')

@section('header')
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Items</h1>
        <h2 class="text-sm md:text-base text-gray-600 font-medium mt-1">Manage your warehouse inventory here.</h2>
    </div>
@endsection

@section('content')
<div x-data="{ deleteUrl: '', barcodeUrl: '' }">
    <x-card>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800">
                {{ __('Items List') }}
            </h2>
            <div class="flex flex-col sm:flex-row w-full md:w-auto gap-4">
                {{-- Search Form --}}
                <form method="GET" action="{{ route('items.list') }}" class="relative w-full sm:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Search" 
                        class="w-full sm:w-64 px-4 py-2 pl-10 rounded-xl border-gray-200 focus:border-[#2B8AE2] focus:ring-[#2B8AE2] bg-white/50 backdrop-blur-sm transition-all text-sm"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>

                @can('create', App\Models\Item::class)
                <a href="{{ route('items.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#0D1A63] to-[#1b2f8a] border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:from-[#050B30] hover:to-[#050B30] focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-[#0D1A63]/20">
                    + Add Item
                </a>
                @endcan
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if($items->isEmpty())
            <div class="text-center py-20">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No Items Found</h3>
                <p class="text-gray-500 mb-6">Get started by adding your first item to the inventory.</p>
                @can('create', App\Models\Item::class)
                <a href="{{ route('items.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#0D1A63] to-[#1b2f8a] border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:from-[#050B30] hover:to-[#050B30] transition ease-in-out duration-150">
                    Create First Item
                </a>
                @endcan
            </div>
        @else
            <div class="overflow-x-auto no-scrollbar">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Item Name</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Item Code</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Condition</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stock</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Barcode</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($items as $index => $item)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-3 px-4 text-sm text-gray-500">{{ $items->firstItem() + $index }}</td>
                                <td class="py-3 px-4 text-sm font-medium text-gray-900">{{ $item->item_name }}</td>
                                <td class="py-3 px-4 text-sm text-gray-600">
                                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-700">
                                        {{ ucfirst($item->type) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-sm font-mono text-gray-600">{{ $item->item_code }}</td>
                                <td class="py-3 px-4">
                                    @if($item->condition == 'good')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <span class="w-2 h-2 bg-green-500 rounded-full mr-1 my-auto"></span> Good
                                        </span>
                                    @elseif($item->condition == 'defect')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            <span class="w-2 h-2 bg-yellow-500 rounded-full mr-1 my-auto"></span> Defect
                                        </span>
                                    @else
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            <span class="w-2 h-2 bg-red-500 rounded-full mr-1 my-auto"></span> Damage
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-700 font-semibold">
                                    {{ $item->stock }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex space-x-2">
                                        <button 
                                            @click="$dispatch('open-modal', 'barcode-modal'); barcodeUrl = '{{ route('items.previewBarcode', $item) }}'"
                                            class="inline-flex items-center p-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors border border-gray-200" 
                                            title="View Barcode"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <a href="{{ route('items.downloadBarcode', $item) }}" class="inline-flex items-center p-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors border border-gray-200" title="Download Barcode">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex space-x-2">
                                        {{-- Edit Button --}}
                                        @can('update', $item)
                                        <a href="{{ route('items.edit', $item) }}" class="p-1 text-gray-400 hover:text-[#E2852E] transition-colors rounded">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        @endcan
                                        
                                        {{-- Delete Button (Triggers Modal) --}}
                                        @can('delete', $item)
                                        <x-button.icon color="red" @click="$dispatch('open-modal', 'delete-modal'); deleteUrl = '{{ route('items.destroy', $item) }}'">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </x-button.icon>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $items->links() }}
            </div>
        @endif
    </x-card>

    <!-- Barcode View Modal -->
    <x-modal name="barcode-modal" maxWidth="md" focusable>
        <div class="p-6 text-center">
            <div class="mb-5 flex justify-center">
                 <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75z" />
                    </svg>
                 </div>
            </div>
            <h3 class="mb-4 text-lg font-normal text-gray-800">Barcode Preview</h3>
            
            <div class="mb-6 flex justify-center p-4 bg-white rounded-xl border border-gray-100 shadow-inner">
                <template x-if="barcodeUrl">
                    <img :src="barcodeUrl" alt="Barcode" class="h-16 w-auto max-w-full">
                </template>
            </div>
            
            <div class="flex justify-center">
                <x-button.primary @click="$dispatch('close')">
                    Close
                </x-button.primary>
            </div>
        </div>
    </x-modal>

    <!-- Custom Delete Confirmation Modal -->
    <x-modal name="delete-modal" maxWidth="md" focusable>
        <div class="p-6 text-center">
            <div class="mb-5 flex justify-center">
                 <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                 </div>
            </div>
            <h3 class="mb-5 text-lg font-normal text-gray-800">Do you want to delete this item?</h3>
            
            <div class="flex justify-center gap-4">
                <button
                    @click="$dispatch('close')"
                    type="button"
                    class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-[#2B8AE2] focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 transition-colors"
                >
                    Cancel
                </button>
                <form :action="deleteUrl" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 shadow-lg shadow-red-600/20 transition-all transform hover:scale-[1.02]"
                    >
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </x-modal>

    <!-- Unauthorized Modal -->
    <x-modal.unauthorized />
</div>
@endsection
