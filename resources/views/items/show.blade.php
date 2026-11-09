@extends('layouts.admin')

@section('title', 'Item Details')

@section('header')
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Item Details</h1>
        <h2 class="text-sm md:text-base text-gray-600 font-medium mt-1">View detailed information about the item.</h2>
    </div>
@endsection

@section('content')
<div x-data="{ showUnauthorizedModal: false }">
    <div class="glass-card rounded-[24px] p-6 max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Item Information</h2>
        <a href="{{ route('items.list') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 bg-white/50 hover:bg-white/80 rounded-xl transition-colors border border-gray-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to List
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-6">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Item Name</label>
                <p class="text-lg font-medium text-gray-900 bg-white/50 p-3 rounded-xl border border-gray-100">{{ $item->item_name }}</p>
            </div>
            
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Type</label>
                <p class="text-base text-gray-800 bg-white/50 p-3 rounded-xl border border-gray-100">{{ ucfirst($item->type) }}</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Item Code</label>
                <p class="text-base font-mono text-gray-800 bg-white/50 p-3 rounded-xl border border-gray-100">{{ $item->item_code }}</p>
            </div>
        </div>

        <div class="space-y-6">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Stock Status</label>
                <div class="bg-white/50 p-3 rounded-xl border border-gray-100 flex justify-between items-center">
                    <span class="text-gray-800 font-medium">{{ $item->stock }} Units</span>
                    @if($item->stock > 10)
                        <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">In Stock</span>
                    @elseif($item->stock > 0)
                        <span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-700 rounded-full">Low Stock</span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded-full">Out of Stock</span>
                    @endif
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Condition</label>
                <div class="bg-white/50 p-3 rounded-xl border border-gray-100">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium
                        {{ $item->condition === 'good' ? 'bg-green-100 text-green-700' : 
                           ($item->condition === 'defect' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                        <span class="w-2 h-2 rounded-full 
                            {{ $item->condition === 'good' ? 'bg-green-500' : 
                               ($item->condition === 'defect' ? 'bg-yellow-500' : 'bg-red-500') }}"></span>
                        {{ ucfirst($item->condition) }}
                    </span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Barcode</label>
                <div class="bg-white p-4 rounded-xl border border-gray-200 flex justify-center items-center">
                    <img src="{{ route('items.previewBarcode', $item) }}" alt="Barcode for {{ $item->item_code }}" class="max-h-20">
                </div>
                <div class="mt-2 text-center">
                    @if(Auth::user()->role === 'supervisor')
                        <button @click="showUnauthorizedModal = true" class="text-xs text-[#2B8AE2] hover:underline font-medium bg-transparent border-0 p-0 cursor-pointer">Download PNG</button>
                    @elseif(Auth::user()->can('downloadBarcode', $item))
                        <a href="{{ route('items.downloadBarcode', $item) }}" class="text-xs text-[#2B8AE2] hover:underline font-medium">Download PNG</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    @if(Auth::user()->role === 'supervisor')
    <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end gap-3">
        <button @click="showUnauthorizedModal = true" class="px-6 py-2.5 bg-gradient-to-r from-[#0D1A63] to-[#1b2f8a] text-white rounded-xl font-medium hover:from-[#050B30] hover:to-[#050B30] transition-colors shadow-lg shadow-[#0D1A63]/20">
            Edit Item
        </button>
    </div>
    @elseif(Auth::user()->can('update', $item))
    <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end gap-3">
        <a href="{{ route('items.edit', $item) }}" class="px-6 py-2.5 bg-gradient-to-r from-[#0D1A63] to-[#1b2f8a] text-white rounded-xl font-medium hover:from-[#050B30] hover:to-[#050B30] transition-colors shadow-lg shadow-[#0D1A63]/20">
            Edit Item
        </a>
    </div>
    @endif

    </div>

    <!-- Unauthorized Modal -->
    <div
        x-show="showUnauthorizedModal"
        style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/40 backdrop-blur-sm p-4"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div
            class="relative w-full max-w-md rounded-[24px] bg-white/90 backdrop-blur-xl border border-white/50 shadow-2xl p-6 text-center transform transition-all"
            @click.away="showUnauthorizedModal = false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <div class="absolute top-4 right-4">
                <button @click="showUnauthorizedModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <h3 class="text-xl font-bold text-gray-900 mb-6">Akses Ditolak</h3>
            
            <div class="mb-6 flex justify-center">
                 <div class="flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-500">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                 </div>
            </div>
            
            <p class="text-lg font-medium text-gray-800 mb-2">Anda tidak memiliki akses ini</p>
            <p class="text-sm text-gray-500 mb-8 px-4">
                Sebagai supervisor, Anda hanya dapat melihat data. Silakan hubungi administrator untuk melakukan perubahan.
            </p>
            
            <div class="flex justify-center">
                <button
                    @click="showUnauthorizedModal = false"
                    type="button"
                    class="rounded-xl bg-gradient-to-r from-[#0D1A63] to-[#1b2f8a] px-8 py-2.5 text-sm font-medium text-white hover:from-[#050B30] hover:to-[#050B30] focus:outline-none focus:ring-4 focus:ring-[#0D1A63]/30 shadow-lg shadow-[#0D1A63]/20 transition-all transform hover:scale-[1.02]"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
