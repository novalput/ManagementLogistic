@props(['name' => 'unauthorized-access'])

<x-modal :name="$name" maxWidth="md" focusable>
    <div class="p-6 text-center">
        <div class="absolute top-4 right-4">
            <button x-on:click="$dispatch('close')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="mb-6 flex justify-center">
             <div class="flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-500">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
             </div>
        </div>
        
        <h3 class="text-xl font-bold text-gray-900 mb-2">Access Denied</h3>
        
        <p class="text-lg font-medium text-gray-800 mb-2">You don't have permission</p>
        <p class="text-sm text-gray-500 mb-8 px-4">
            As a supervisor, you can only view data. Please contact an administrator to make changes.
        </p>
        
        <div class="flex justify-center">
            <button
                x-on:click="$dispatch('close')"
                type="button"
                class="rounded-xl bg-gradient-to-r from-[#0D1A63] to-[#1b2f8a] px-8 py-2.5 text-sm font-medium text-white hover:from-[#050B30] hover:to-[#050B30] focus:outline-none focus:ring-4 focus:ring-[#0D1A63]/30 shadow-lg shadow-[#0D1A63]/20 transition-all transform hover:scale-[1.02]"
            >
                Close
            </button>
        </div>
    </div>
</x-modal>
