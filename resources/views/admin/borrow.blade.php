@extends('layouts.admin')

@section('title', 'Borrow')

@section('header')
<div>
    <h1 class="text-3xl font-bold text-gray-900">Borrow {{ ucfirst($type) }} Items</h1>
    <p class="text-gray-600 mt-1">Create and authorize a new {{ $type }} item borrowing request for staffs.</p>
</div>
@endsection

@section('content')
<div class="h-full flex flex-col" x-data="borrowPage()" x-init="init()">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        @if(Auth::user()->role === 'admin')
        <button
            type="button"
            @click="openScanModal()"
            title="Scan Item Barcode / QR Code"
            class="flex items-center justify-center gap-2 md:gap-3 h-[48px] md:h-[52px] px-6 md:px-8 py-3 md:py-4 rounded-full bg-[#F7F5FF] text-gray-800 text-sm md:text-base font-medium hover:bg-[#E6ECFF] transition-colors"
            style="border: 2px solid #2B8AE2 !important;"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 md:w-6 md:h-6" viewBox="0 -960 960 960" fill="currentColor">
                <path d="M40-120v-200h80v120h120v80H40Zm680 0v-80h120v-120h80v200H720ZM160-240v-480h80v480h-80Zm120 0v-480h40v480h-40Zm120 0v-480h80v480h-80Zm120 0v-480h120v480H520Zm160 0v-480h40v480h-40Zm80 0v-480h40v480h-40ZM40-640v-200h200v80H120v120H40Zm800 0v-120H720v-80h200v200h-80Z"/>
            </svg>
            <span class="hidden sm:inline">Scan Item</span>
            <span class="sm:hidden">Scan</span>
        </button>
        @else
        <button
            type="button"
            @click="showUnauthorizedModal = true"
            title="Anda tidak memiliki akses ini"
            class="flex items-center justify-center gap-2 md:gap-3 h-[48px] md:h-[52px] px-6 md:px-8 py-3 md:py-4 rounded-full bg-[#F7F5FF] text-gray-800 text-sm md:text-base font-medium hover:bg-[#E6ECFF] transition-colors"
            style="border: 2px solid #2B8AE2 !important;"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 md:w-6 md:h-6" viewBox="0 -960 960 960" fill="currentColor">
                <path d="M40-120v-200h80v120h120v80H40Zm680 0v-80h120v-120h80v200H720ZM160-240v-480h80v480h-80Zm120 0v-480h40v480h-40Zm120 0v-480h80v480h-80Zm120 0v-480h120v480H520Zm160 0v-480h40v480h-40Zm80 0v-480h40v480h-40ZM40-640v-200h200v80H120v120H40Zm800 0v-120H720v-80h200v200h-80Z"/>
            </svg>
            <span class="hidden sm:inline">Scan Item</span>
            <span class="sm:hidden">Scan</span>
        </button>
        @endif
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- Main Content Grid -->
    <div class="flex flex-col gap-6">
        <!-- Borrower Information Card -->
        <div class="glass-card rounded-[24px] p-6 flex flex-col">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Borrower Information</h2>
            </div>

            <form id="borrowForm" method="POST" action="{{ route('borrow.store', ['type' => $type]) }}" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @csrf

                <!-- Borrower Name Input -->
                <div>
                    <label for="borrower_name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Borrower Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="borrower_name"
                        name="borrower_name"
                        x-model="form.borrower_name"
                        @input="validateField('borrower_name')"
                        :class="errors.borrower_name ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-[#2B8AE2] focus:ring-[#2B8AE2]'"
                        class="w-full rounded-lg text-sm py-2.5 px-4 bg-white/50 transition-colors"
                        placeholder="Enter borrower name"
                        required
                    >
                    <p x-show="errors.borrower_name" x-text="errors.borrower_name" class="mt-1 text-xs text-red-600"></p>
                </div>

                <!-- Division Dropdown -->
                <div>
                    <label for="borrower_division" class="block text-sm font-semibold text-gray-700 mb-2">
                        Division <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="borrower_division"
                        name="borrower_division"
                        x-model="form.borrower_division"
                        @change="handleDivisionChange()"
                        :class="errors.borrower_division ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-[#2B8AE2] focus:ring-[#2B8AE2]'"
                        class="w-full rounded-lg text-sm py-2.5 px-4 bg-white/50 transition-colors appearance-none cursor-pointer"
                        required
                    >
                        <option value="">Select Division</option>
                        <template x-for="division in divisions" :key="division">
                            <option :value="division" x-text="division"></option>
                        </template>
                        <option value="__add_new__">+ Add New Division</option>
                    </select>
                    <p x-show="errors.borrower_division" x-text="errors.borrower_division" class="mt-1 text-xs text-red-600"></p>
                    
                    <!-- Add New Division Input -->
                    <div x-show="showAddDivisionInput" x-transition class="mt-3">
                        <label for="new_division" class="block text-sm font-semibold text-gray-700 mb-2">
                            New Division Name <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <input
                                type="text"
                                id="new_division"
                                x-model="newDivisionName"
                                @keydown.enter.prevent="addNewDivision()"
                                @input="newDivisionError = ''"
                                :class="newDivisionError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-[#2B8AE2] focus:ring-[#2B8AE2]'"
                                class="flex-1 rounded-lg text-sm py-2.5 px-4 bg-white/50 transition-colors"
                                placeholder="Enter new division name"
                            >
                            <button
                                type="button"
                                @click="addNewDivision()"
                                class="px-4 py-2.5 bg-gradient-to-r from-[#0D1A63] to-[#1b2f8a] text-white font-semibold rounded-lg hover:from-[#050B30] hover:to-[#050B30] transition-colors text-sm"
                            >
                                Add
                            </button>
                            <button
                                type="button"
                                @click="cancelAddDivision()"
                                class="px-4 py-2.5 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-colors text-sm"
                            >
                                Cancel
                            </button>
                        </div>
                        <p x-show="newDivisionError" x-text="newDivisionError" class="mt-1 text-xs text-red-600"></p>
                    </div>
                </div>

                <!-- Borrow Date -->
                <div>
                    <label for="borrow_date" class="block text-sm font-semibold text-gray-700 mb-2">
                        Borrow Date <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="date"
                            id="borrow_date"
                            name="borrow_date"
                            x-model="form.borrow_date"
                            @change="validateField('borrow_date'); validateField('due_date')"
                            :min="minDate"
                            :class="errors.borrow_date ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-[#2B8AE2] focus:ring-[#2B8AE2]'"
                            class="w-full rounded-lg text-sm py-2.5 px-4 bg-white/50 transition-colors"
                            required
                        >
                    </div>
                    <p x-show="errors.borrow_date" x-text="errors.borrow_date" class="mt-1 text-xs text-red-600"></p>
                </div>

                <!-- Return Date -->
                @if($type === 'returnable')
                <div>
                    <label for="due_date" class="block text-sm font-semibold text-gray-700 mb-2">
                        Return Date <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="date"
                            id="due_date"
                            name="due_date"
                            x-model="form.due_date"
                            @change="validateField('due_date')"
                            :min="form.borrow_date || minDate"
                            :class="errors.due_date ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-[#2B8AE2] focus:ring-[#2B8AE2]'"
                            class="w-full rounded-lg text-sm py-2.5 px-4 bg-white/50 transition-colors"
                            required
                        >
                    </div>
                    <p x-show="errors.due_date" x-text="errors.due_date" class="mt-1 text-xs text-red-600"></p>
                </div>
                @endif

                <!-- Purpose Textarea -->
                <div class="md:col-span-2 flex flex-col">
                    <label for="purpose" class="block text-sm font-semibold text-gray-700 mb-2">
                        Purpose
                    </label>
                    <textarea
                        id="purpose"
                        name="purpose"
                        x-model="form.purpose"
                        @input="updateCharCount()"
                        :class="errors.purpose ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-[#2B8AE2] focus:ring-[#2B8AE2]'"
                        class="w-full rounded-lg text-sm py-2.5 px-4 bg-white/50 transition-colors resize-none flex-1 min-h-[120px]"
                        placeholder="Enter purpose of borrowing (optional)"
                        maxlength="1000"
                    ></textarea>
                    <div class="flex justify-between items-center mt-2">
                        <p x-show="errors.purpose" x-text="errors.purpose" class="text-xs text-red-600"></p>
                        <p class="text-xs text-gray-500 ml-auto">
                            <span x-text="charCount"></span> / 1000 characters
                        </p>
                    </div>
                </div>
            </form>
        </div>

        <!-- Items Scanned Card (unchanged structure, just wrapper check) -->
        <div class="glass-card rounded-[24px] min-h-[500px] p-6 flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Items Scanned</h2>
                </div>
                <span 
                    x-show="scannedItems.length > 0"
                    class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-bold border border-yellow-200"
                    x-text="scannedItems.length + ' Items'"
                ></span>
            </div>

            <!-- Items Table -->
            <div class="flex-1 overflow-y-auto custom-scrollbar" x-show="scannedItems.length > 0">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 bg-white/80 backdrop-blur-sm z-10">
                        <tr class="text-gray-500 text-xs border-b border-gray-200/50 uppercase tracking-wider">
                            <th class="py-3 px-4 font-semibold">Item Code</th>
                            <th class="py-3 px-4 font-semibold">Item Name</th>
                            <th class="py-3 px-4 font-semibold w-24">Qty</th>
                            <th class="py-3 px-4 font-semibold">Condition</th>
                            <th class="py-3 px-4 font-semibold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <template x-for="(item, index) in scannedItems" :key="item.id + '-' + index">
                            <tr class="border-b border-gray-100 last:border-0 hover:bg-white/40 transition-colors">
                                <td class="py-3 px-4 font-mono text-gray-600" x-text="item.item_code"></td>
                                <td class="py-3 px-4 font-medium text-gray-900" x-text="item.item_name"></td>
                                <td class="py-3 px-4">
                                    <input 
                                        type="number" 
                                        x-model.number="item.quantity"
                                        @input="validateQuantity(index)"
                                        class="w-20 rounded-md border-gray-300 text-sm focus:border-[#2B8AE2] focus:ring-[#2B8AE2]"
                                        min="1"
                                        :max="item.availableItems.find(i => i.id == item.selected_id)?.stock || 1"
                                    >
                                    <div class="text-xs text-gray-500 mt-1">Max: <span x-text="item.availableItems.find(i => i.id == item.selected_id)?.stock || 1"></span></div>
                                </td>
                                <td class="py-3 px-4">
                                    <select x-model="item.selected_id" @change="item.quantity = 1" class="w-full rounded-md border-gray-300 text-sm py-1.5 focus:border-[#2B8AE2] focus:ring-[#2B8AE2]">
                                        <template x-for="avail in item.availableItems" :key="avail.id">
                                            <option :value="avail.id" 
                                                    :disabled="scannedItems.some(i => i !== item && i.item_code === item.item_code && i.selected_id == avail.id)"
                                                    x-text="avail.condition.charAt(0).toUpperCase() + avail.condition.slice(1) + ' (' + avail.stock + ')'"></option>
                                        </template>
                                    </select>
                                    <button type="button" 
                                            x-show="item.availableItems.length > scannedItems.filter(i => i.item_code === item.item_code).length" 
                                            @click.prevent="duplicateItemRow(index)" 
                                            class="text-xs text-[#2B8AE2] hover:text-blue-800 mt-1 block font-medium">
                                        + Add other condition
                                    </button>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <button
                                        @click="removeItem(index)"
                                        class="p-1.5 text-gray-400 hover:text-red-600 transition-colors"
                                        title="Remove item"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div 
                x-show="scannedItems.length === 0"
                class="flex-1 flex flex-col items-center justify-center text-gray-500 py-12"
            >
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                </div>
                <p class="text-lg font-semibold text-gray-700 mb-1">No items scanned</p>
                <p class="text-sm text-gray-500">Click "Scan Item" to add items</p>
            </div>

            <!-- Confirm Button -->
            <div class="mt-6 pt-6 border-t border-gray-200/50">
                @if(Auth::user()->role === 'admin')
                <button
                    @click="confirmBorrowing()"
                    class="w-full py-3 px-4 text-white font-bold rounded-lg transition-colors shadow-md flex items-center justify-center gap-2 bg-gradient-to-r from-[#0D1A63] to-[#1b2f8a] hover:from-[#050B30] hover:to-[#050B30] cursor-pointer"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Confirm Borrowing
                </button>
                @else
                <button
                    @click="showUnauthorizedModal = true"
                    type="button"
                    class="w-full py-3 px-4 text-white font-bold rounded-lg transition-colors shadow-md flex items-center justify-center gap-2 bg-gradient-to-r from-[#0D1A63] to-[#1b2f8a] hover:from-[#050B30] hover:to-[#050B30]"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Confirm Borrowing
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Scan Item Modal -->
    <div 
        x-show="showScanModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        @click.away="closeScanModal()"
        style="display: none;"
    >
        <div 
            @click.stop
            class="bg-white rounded-[24px] p-8 max-w-md w-full shadow-xl"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Scan Item</h3>
                <button @click="closeScanModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Item Code / Barcode</label>
                    <input
                        type="text"
                        x-model="scanInput"
                        @keydown.enter.prevent="scanItem()"
                        @input="scanError = ''"
                        class="w-full rounded-lg text-sm py-3 px-4 bg-white/50 border border-gray-300 focus:border-[#2B8AE2] focus:ring-[#2B8AE2]"
                        placeholder="Enter or scan item code"
                        autofocus
                    >
                    <p x-show="scanError" x-text="scanError" class="mt-2 text-sm text-red-600"></p>
                </div>

                <div class="flex gap-3">
                    <button
                        @click="scanItem()"
                        :disabled="scanning || !scanInput"
                        class="flex-1 py-3 px-4 bg-gradient-to-r from-[#0D1A63] to-[#1b2f8a] text-white font-semibold rounded-lg hover:from-[#050B30] hover:to-[#050B30] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <svg x-show="scanning" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-show="!scanning">Scan</span>
                        <span x-show="scanning">Scanning...</span>
                    </button>
                    <button
                        @click="closeScanModal()"
                        class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-colors"
                    >
                        Cancel
                    </button>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Or upload barcode image</label>
                    <input 
                        type="file" 
                        accept="image/*" 
                        @change="handleFileUpload($event)"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#0D1A63]/10 file:text-[#0D1A63] hover:file:bg-[#0D1A63]/20 cursor-pointer transition-colors"
                    >
                    <div id="reader" class="hidden"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div 
        x-show="showConfirmModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        @click.away="showConfirmModal = false"
        style="display: none;"
    >
        <div 
            @click.stop
            class="bg-white rounded-[24px] p-8 max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-xl"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Confirm Borrowing Request</h3>
                <button @click="showConfirmModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-6">
                <!-- Borrower Info -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-3">Borrower Information</h4>
                    <div class="bg-white/50 rounded-lg p-4 space-y-2">
                        <p><span class="font-semibold">Name:</span> <span x-text="form.borrower_name"></span></p>
                        <p><span class="font-semibold">Division:</span> <span x-text="form.borrower_division"></span></p>
                        <p><span class="font-semibold">Borrow Date:</span> <span x-text="formatDate(form.borrow_date)"></span></p>
                        @if ($type == 'returnable')
                        <p><span class="font-semibold">Return Date:</span> <span x-text="formatDate(form.due_date)"></span></p>
                        @endif
                        <p><span class="font-semibold">Purpose:</span> <span x-text="form.purpose"></span></p>
                    </div>
                </div>

                <!-- Items List -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-3">Items (<span x-text="scannedItems.length"></span>)</h4>
                    <div class="bg-white/50 rounded-lg p-4 space-y-2 max-h-48 overflow-y-auto">
                        <template x-for="item in scannedItems" :key="item.id">
                            <p class="text-sm">• <span x-text="item.item_name"></span> (<span x-text="item.item_code"></span>)</p>
                        </template>
                    </div>
                </div>

                <div class="flex gap-3 pt-4 border-t border-gray-200">
                    <button
                        @click="submitForm()"
                        :disabled="submitting"
                        class="flex-1 py-3 px-4 bg-gradient-to-r from-[#0D1A63] to-[#1b2f8a] text-white font-semibold rounded-lg hover:from-[#050B30] hover:to-[#050B30] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <svg x-show="submitting" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-show="!submitting">Confirm</span>
                        <span x-show="submitting">Submitting...</span>
                    </button>
                    <button
                        @click="showConfirmModal = false"
                        class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-colors"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Toast Notification -->
    <div 
        x-show="toast.show"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-full opacity-0"
        class="fixed bottom-6 right-6 z-50"
        style="display: none;"
    >
        <div 
            class="px-6 py-4 rounded-lg shadow-lg flex items-center gap-3"
            :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="toast.type === 'success'">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="toast.type === 'error'">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <span x-text="toast.message"></span>
        </div>
    </div>

    <!-- Unauthorized Access Modal -->
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

    <!-- Delete Item Confirmation Modal -->
    <div 
        x-show="showDeleteItemModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[60] flex items-center justify-center p-4"
        @click.away="showDeleteItemModal = false"
        style="display: none;"
    >
        <div class="bg-white rounded-[24px] p-8 max-w-sm w-full shadow-xl text-center" @click.stop
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">
            <div class="mb-5 flex justify-center">
                 <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                 </div>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Remove Item</h3>
            <p class="text-gray-500 mb-6">Are you sure you want to remove this item from the list?</p>
            <div class="flex justify-center gap-3">
                <button @click="showDeleteItemModal = false" class="px-5 py-2.5 bg-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-300 transition-colors">Cancel</button>
                <button @click="confirmRemoveItem()" class="px-5 py-2.5 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 transition-colors shadow-lg shadow-red-600/20">Remove</button>
            </div>
        </div>
    </div>
</div>

<script>
function borrowPage() {
    return {
        form: {
            borrower_name: '',
            borrower_division: '',
            borrow_date: '',
            due_date: '',
            purpose: ''
        },
        errors: {},
        scannedItems: [],
        showScanModal: false,
        showConfirmModal: false,
        showUnauthorizedModal: false,
        showDeleteItemModal: false,
        itemToRemoveIndex: null,
        users: @json($users ?? []),
        scanInput: '',
        scanError: '',
        scanning: false,
        submitting: false,
        charCount: 0,
        minDate: new Date().toISOString().split('T')[0],
        divisions: @json($divisions ?? []),
        showAddDivisionInput: false,
        newDivisionName: '',
        newDivisionError: '',
        toast: {
            show: false,
            type: 'success',
            message: ''
        },

        init() {
            this.updateCharCount();
            // Set default dates
            const today = new Date();
            this.form.borrow_date = today.toISOString().split('T')[0];
            const nextWeek = new Date(today);
            nextWeek.setDate(today.getDate() + 7);
            this.form.due_date = nextWeek.toISOString().split('T')[0];
        },

        get isAllConsumable() {
            return '{{ $type }}' === 'consumable';
        },

        selectUser(userId) {
            // Deprecated: No longer selecting user from dropdown
        },

        handleDivisionChange() {
            if (this.form.borrower_division === '__add_new__') {
                this.showAddDivisionInput = true;
                this.form.borrower_division = '';
                setTimeout(() => {
                    const input = document.getElementById('new_division');
                    if (input) input.focus();
                }, 100);
            } else {
                this.showAddDivisionInput = false;
                this.newDivisionName = '';
                this.newDivisionError = '';
                this.validateField('borrower_division');
            }
        },

        addNewDivision() {
            this.newDivisionError = '';
            
            if (!this.newDivisionName || !this.newDivisionName.trim()) {
                this.newDivisionError = 'Division name cannot be empty';
                return;
            }

            const trimmedName = this.newDivisionName.trim();
            
            // Check if division already exists
            if (this.divisions.includes(trimmedName)) {
                this.newDivisionError = 'This division already exists';
                return;
            }

            // Add new division to the list
            this.divisions.push(trimmedName);
            
            // Set newly added division after DOM updates
            this.$nextTick(() => {
                this.form.borrower_division = trimmedName;
                this.validateField('borrower_division');
            });
            
            // Hide the input and reset
            this.showAddDivisionInput = false;
            this.newDivisionName = '';
            this.newDivisionError = '';
            
            // Validate the field
            this.validateField('borrower_division');
            
            this.showToast('New division added successfully', 'success');
        },

        cancelAddDivision() {
            this.showAddDivisionInput = false;
            this.newDivisionName = '';
            this.newDivisionError = '';
            this.form.borrower_division = '';
        },

        validateField(field) {
            this.errors[field] = '';
            
            switch(field) {
                case 'borrower_name':
                    if (!this.form.borrower_name) {
                        this.errors.borrower_name = 'Borrower name is required';
                    }
                    break;
                case 'borrower_division':
                    if (!this.form.borrower_division) {
                        this.errors.borrower_division = 'Division is required';
                    }
                    break;
                case 'borrow_date':
                    if (!this.form.borrow_date) {
                        this.errors.borrow_date = 'Borrow date is required';
                    } else {
                        const date = new Date(this.form.borrow_date);
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);
                        if (date < today) {
                            this.errors.borrow_date = 'Cannot select past dates';
                        }
                    }
                    break;
                case 'due_date':
                    if (this.isAllConsumable) {
                        this.errors.due_date = '';
                        // clear due date validation if it's consumable
                    } else if (!this.form.due_date) {
                        this.errors.due_date = 'Return date is required';
                    } else if (this.form.borrow_date) {
                        const borrowDate = new Date(this.form.borrow_date);
                        const dueDate = new Date(this.form.due_date);
                        if (dueDate <= borrowDate) {
                            this.errors.due_date = 'Return date must be later than borrow date';
                        }
                    }
                    break;
                case 'purpose':
                    if (this.form.purpose && this.form.purpose.length > 1000) {
                        this.errors.purpose = 'Purpose must not exceed 1000 characters';
                    }
                    break;
            }
        },

        get isFormValid() {
            const hasDueDate = this.isAllConsumable ? true : (this.form.due_date && !this.errors.due_date);
            return !this.errors.borrower_name &&
                   !this.errors.borrower_division &&
                   !this.errors.borrow_date &&
                   hasDueDate &&
                   !this.errors.purpose &&
                   this.form.borrower_name &&
                   this.form.borrower_division &&
                   this.form.borrow_date &&
                   this.scannedItems.length > 0;
        },

        updateCharCount() {
            this.charCount = this.form.purpose ? this.form.purpose.length : 0;
        },

        openScanModal() {
            this.showScanModal = true;
            this.scanInput = '';
            this.scanError = '';
            setTimeout(() => {
                const input = document.querySelector('[x-model="scanInput"]');
                if (input) input.focus();
            }, 100);
        },

        closeScanModal() {
            this.showScanModal = false;
            this.scanInput = '';
            this.scanError = '';
        },

        async scanItem() {
            if (!this.scanInput.trim()) {
                this.scanError = 'Please enter item code';
                return;
            }

            this.scanning = true;
            this.scanError = '';

            try {
                const response = await fetch('{{ route("borrow.scan") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        item_code: this.scanInput.trim()
                    })
                });
                
                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    const text = await response.text();
                    console.error("Non-JSON response:", text);
                    throw new Error("Server returned non-JSON response: " + (text.substring(0, 100) + "..."));
                }

                const data = await response.json();

                if (data.success && data.items && data.items.length > 0) {
                    const code = data.items[0].item_code;
                    // Check if already in list
                    const existingIdx = this.scannedItems.findIndex(i => i.item_code === code);
                    
                    if(existingIdx >= 0) {
                        const row = this.scannedItems[existingIdx];
                        const avail = row.availableItems.find(i => i.id == row.selected_id);
                        if(row.quantity < avail.stock) {
                            row.quantity++;
                        } else {
                            this.scanError = 'Maximum available stock reached for selected condition';
                            this.scanning = false;
                            return;
                        }
                    } else {
                        const def = data.items.find(i => i.condition === 'good') || data.items[0];
                        if (def.type !== '{{ $type }}') {
                            this.scanError = `Please scan a {{ $type }} item. This item is ${def.type}.`;
                            this.scanning = false;
                            return;
                        }

                        this.scannedItems.push({
                            id: Date.now(),
                            item_code: code,
                            item_name: def.item_name,
                            availableItems: data.items,
                            selected_id: def.id,
                            quantity: 1
                        });
                        
                        this.validateField('due_date');
                        this.showToast('Item scanned successfully', 'success');
                    }

                    this.scanInput = '';
                    this.closeScanModal();
                } else {
                    this.scanError = data.message || 'Item not found';
                }
            } catch (error) {
                console.error('Scan error:', error);
                this.scanError = error.message || 'Error scanning item. Please try again.';
            } finally {
                this.scanning = false;
            }
        },

        async handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.scanning = true;
            this.scanError = '';

            try {
                // Ensure Html5Qrcode is loaded
                if (typeof Html5Qrcode === 'undefined') {
                    throw new Error('QR/Barcode scanner library not loaded');
                }

                const html5QrCode = new Html5Qrcode("reader");
                const decodedText = await html5QrCode.scanFile(file, false);
                
                if (decodedText) {
                    this.scanInput = decodedText;
                    await this.scanItem();
                    // Clear the file input
                    event.target.value = '';
                    // Clear instance
                    html5QrCode.clear();
                }
            } catch (err) {
                console.error('Barcode scan error:', err);
                this.scanError = 'Could not read barcode from image. Please ensure the image is clear and contains a valid barcode.';
                this.scanning = false;
                event.target.value = ''; // Reset input
            }
        },

        validateQuantity(index) {
            const item = this.scannedItems[index];
            const avail = item.availableItems.find(i => i.id == item.selected_id);
            if (!avail) return;
            
            if (item.quantity > avail.stock) {
                item.quantity = avail.stock;
                this.showToast(`Max available stock is ${avail.stock}`, 'error');
            }
            if (item.quantity < 1) {
                item.quantity = 1;
            }
        },

        duplicateItemRow(index) {
            const item = this.scannedItems[index];
            const usedIds = this.scannedItems
                .filter(i => i.item_code === item.item_code)
                .map(i => i.selected_id);
            
            const unusedAvail = item.availableItems.find(i => !usedIds.includes(i.id));
            if (!unusedAvail) return;

            this.scannedItems.splice(index + 1, 0, {
                id: Date.now() + Math.random(),
                item_code: item.item_code,
                item_name: item.item_name,
                availableItems: item.availableItems,
                selected_id: unusedAvail.id,
                quantity: 1
            });
            this.validateField('due_date');
        },

        removeItem(index) {
            this.itemToRemoveIndex = index;
            this.showDeleteItemModal = true;
        },

        confirmRemoveItem() {
            if (this.itemToRemoveIndex !== null) {
                this.scannedItems.splice(this.itemToRemoveIndex, 1);
                this.itemToRemoveIndex = null;
                this.validateField('due_date');
            }
            this.showDeleteItemModal = false;
        },

        confirmBorrowing() {
            // Validate all fields
            Object.keys(this.form).forEach(key => {
                this.validateField(key);
            });

            // Check if borrower info is filled
            const hasDueDate = this.isAllConsumable ? true : this.form.due_date;
            const isBorrowerInfoFilled = this.form.borrower_name && 
                                       this.form.borrower_division && 
                                       this.form.borrow_date && 
                                       hasDueDate;

            if (!isBorrowerInfoFilled) {
                this.showToast('Borrower form must be filled', 'error');
                return;
            }

            // Check if items are scanned
            if (this.scannedItems.length === 0) {
                this.showToast('Please scan at least one item', 'error');
                return;
            }

            // Check for specific validation errors
            if (this.errors.borrower_name || this.errors.borrower_division || 
                this.errors.borrow_date || this.errors.due_date || this.errors.purpose) {
                this.showToast('Please correct the errors in the form', 'error');
                return;
            }

            this.showConfirmModal = true;
        },

        async submitForm() {
            this.submitting = true;

            try {
                const form = document.getElementById('borrowForm');
                const formData = new FormData(form);

                // Add items to form data
                this.scannedItems.forEach((item, index) => {
                    formData.append(`items[${index}][item_id]`, item.selected_id);
                    formData.append(`items[${index}][quantity]`, item.quantity);
                });

                const response = await fetch('{{ route("borrow.store", ["type" => $type]) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    this.showToast('Borrowing Confirmed', 'success');
                    this.showConfirmModal = false;
                    
                    // Reset form
                    setTimeout(() => {
                        this.form = {
                            borrower_name: '',
                            borrower_division: '',
                            borrow_date: this.minDate,
                            due_date: '',
                            purpose: ''
                        };
                        this.scannedItems = [];
                        this.errors = {};
                        this.updateCharCount();
                        
                        // Redirect or reload
                        window.location.href = "{{ route('borrow.create', ['type' => $type]) }}";
                    }, 1500);
                } else {
                    // Handle validation errors or other messages
                    let errorMessage = data.message || 'Error creating borrowing request';
                    if (data.errors) {
                        // Join all error messages
                        const errorMessages = Object.values(data.errors).flat();
                        errorMessage = errorMessages.join('\n');
                    }
                    this.showToast(errorMessage, 'error');
                }
            } catch (error) {
                this.showToast('Error submitting request. Please try again.', 'error');
                console.error('Submit error:', error);
            } finally {
                this.submitting = false;
            }
        },

        showToast(message, type = 'success') {
            this.toast = { show: true, message, type };
            setTimeout(() => {
                this.toast.show = false;
            }, 3000);
        },

        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
        }
    }
}
</script>

@endsection
