@extends('layouts.admin')

@section('title', 'Edit Item')

@section('header')
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Edit Item</h1>
        <h2 class="text-sm md:text-base text-gray-600 font-medium mt-1">Update item details.</h2>
    </div>
@endsection

@section('content')
<div class="glass-card rounded-2xl p-5 max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Edit Item</h2>
        <a href="{{ route('items.list') }}" class="p-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
    </div>

    <form action="{{ route('items.update', $item) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        @method('PUT')
        
        {{-- Item Code (Read Only) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Item Code</label>
            <input type="text" value="{{ $item->item_code }}" readonly disabled
                class="w-full px-4 py-2 rounded-xl border-gray-200 bg-gray-50 text-gray-500 cursor-not-allowed">
        </div>

        {{-- Stock --}}
        <div>
            <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
            <input type="number" name="stock" id="stock" 
                class="w-full px-4 py-2 rounded-xl border-gray-200 focus:border-[#2B8AE2] focus:ring-[#2B8AE2] bg-white/50 backdrop-blur-sm transition-all"
                value="{{ old('stock', $item->stock) }}" required min="0" placeholder="0">
            @error('stock')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Item Name --}}
        <div class="md:col-span-2">
            <label for="item_name" class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
            <input type="text" name="item_name" id="item_name" 
                class="w-full px-4 py-2 rounded-xl border-gray-200 focus:border-[#2B8AE2] focus:ring-[#2B8AE2] bg-white/50 backdrop-blur-sm transition-all"
                value="{{ old('item_name', $item->item_name) }}" required placeholder="Enter item name">
            @error('item_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Type --}}
        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
            <select name="type" id="type" 
                class="w-full px-4 py-2 rounded-xl border-gray-200 focus:border-[#2B8AE2] focus:ring-[#2B8AE2] bg-white/50 backdrop-blur-sm transition-all" required>
                <option value="returnable" {{ old('type', $item->type) == 'returnable' ? 'selected' : '' }}>Returnable</option>
                <option value="consumable" {{ old('type', $item->type) == 'consumable' ? 'selected' : '' }}>Consumable</option>
            </select>
            @error('type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Condition --}}
        <div>
            <label for="condition" class="block text-sm font-medium text-gray-700 mb-1">Condition</label>
            <select name="condition" id="condition" 
                class="w-full px-4 py-2 rounded-xl border-gray-200 focus:border-[#2B8AE2] focus:ring-[#2B8AE2] bg-white/50 backdrop-blur-sm transition-all">
                <option value="good" {{ old('condition', $item->condition) == 'good' ? 'selected' : '' }}>Good</option>
                <option value="defect" {{ old('condition', $item->condition) == 'defect' ? 'selected' : '' }}>Defect</option>
                <option value="damage" {{ old('condition', $item->condition) == 'damage' ? 'selected' : '' }}>Damage</option>
            </select>
            @error('condition')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit Button --}}
        <div class="md:col-span-2 pt-2">
            <button type="submit" class="w-full px-6 py-2.5 bg-gradient-to-r from-[#0D1A63] to-[#1b2f8a] text-white rounded-xl font-semibold shadow-lg shadow-[#0D1A63]/30 transition-all transform hover:scale-[1.02] hover:from-[#050B30] hover:to-[#050B30]">
                Update Item
            </button>
        </div>
    </form>
</div>
@endsection
