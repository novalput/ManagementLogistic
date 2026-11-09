@extends('layouts.admin')

@section('title', 'Create Item')

@section('header')
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Create Item(s)</h1>
        <h2 class="text-sm md:text-base text-gray-600 font-medium mt-1">Fill the details here to add one or more items.</h2>
    </div>
@endsection

@section('content')
<x-card class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Add New Items</h2>
        <a href="{{ route('items.list') }}" class="p-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
    </div>

    <form action="{{ route('items.store') }}" method="POST" id="create-items-form">
        @csrf
        
        <div id="items-container" class="space-y-6">
            @php $oldItems = old('items', [[]]); @endphp
            @foreach($oldItems as $i => $oldItem)
            <div class="item-row p-4 border border-gray-200 rounded-xl relative bg-gray-50/50">
                <button type="button" class="remove-item absolute top-4 right-4 text-red-500 hover:text-red-700 {{ count($oldItems) > 1 ? '' : 'hidden' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-2">
                    {{-- Item Name --}}
                    <div class="lg:col-span-1">
                        <x-label value="Item Name" />
                        <x-input type="text" name="items[{{ $i }}][item_name]" value="{{ $oldItem['item_name'] ?? '' }}" required placeholder="Item name" class="w-full" />
                        @error("items.{$i}.item_name") <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Type --}}
                    <div>
                        <x-label value="Type" />
                        <x-select name="items[{{ $i }}][type]" required class="w-full">
                            <option value="returnable" {{ ($oldItem['type'] ?? 'returnable') == 'returnable' ? 'selected' : '' }}>Returnable</option>
                            <option value="consumable" {{ ($oldItem['type'] ?? '') == 'consumable' ? 'selected' : '' }}>Consumable</option>
                        </x-select>
                        @error("items.{$i}.type") <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Condition --}}
                    <div>
                        <x-label value="Condition" />
                        <x-select name="items[{{ $i }}][condition]" required class="w-full">
                            <option value="good" {{ ($oldItem['condition'] ?? 'good') == 'good' ? 'selected' : '' }}>Good</option>
                            <option value="defect" {{ ($oldItem['condition'] ?? '') == 'defect' ? 'selected' : '' }}>Defect</option>
                            <option value="damage" {{ ($oldItem['condition'] ?? '') == 'damage' ? 'selected' : '' }}>Damage</option>
                        </x-select>
                        @error("items.{$i}.condition") <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Stock --}}
                    <div>
                        <x-label value="Stock" />
                        <x-input type="number" name="items[{{ $i }}][stock]" value="{{ $oldItem['stock'] ?? '' }}" required min="0" placeholder="0" class="w-full" />
                        @error("items.{$i}.stock") <span class="text-sm text-red-600 mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4 flex justify-between items-center">
            <button type="button" id="add-item-btn" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Another Item
            </button>
        </div>

        <div class="mt-8 pt-5 border-t border-gray-200">
            <x-button.primary type="submit" class="w-full md:w-auto justify-center text-base py-2.5 px-6">
                Save All Items
            </x-button.primary>
        </div>
    </form>
</x-card>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('items-container');
        const addBtn = document.getElementById('add-item-btn');
        let index = {{ count($oldItems) }};

        addBtn.addEventListener('click', function() {
            const rows = container.querySelectorAll('.item-row');
            // Clone the first row as template
            const template = rows[0].cloneNode(true);
            
            // Show remove button
            template.querySelector('.remove-item').classList.remove('hidden');
            
            // Clear inputs, update names, wipe errors
            template.querySelectorAll('input, select').forEach(input => {
                if(input.tagName === 'INPUT') {
                    input.value = '';
                } else if(input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                }
                
                // Update name attributes: items[0][field] -> items[index][field]
                const name = input.getAttribute('name');
                if(name) {
                    input.setAttribute('name', name.replace(/items\[\d+\]/, `items[${index}]`));
                }
            });
            
            // Remove error messages in cloned row
            template.querySelectorAll('.text-red-600').forEach(err => err.remove());

            // Add remove listener
            template.querySelector('.remove-item').addEventListener('click', function() {
                template.remove();
                updateRemoveButtons();
            });

            container.appendChild(template);
            index++;
            updateRemoveButtons();
        });

        // Attach remove listener to existing rows at load
        const rows = container.querySelectorAll('.item-row');
        rows.forEach(row => {
            row.querySelector('.remove-item').addEventListener('click', function() {
                row.remove();
                updateRemoveButtons();
            });
        });

        function updateRemoveButtons() {
            const currentRows = container.querySelectorAll('.item-row');
            currentRows.forEach((row, i) => {
                const removeBtn = row.querySelector('.remove-item');
                if(currentRows.length > 1) {
                    removeBtn.classList.remove('hidden');
                } else {
                    removeBtn.classList.add('hidden');
                }
            });
        }
    });
</script>
@endsection
