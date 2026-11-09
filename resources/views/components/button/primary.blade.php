<button {{ $attributes->merge(['class' => 'inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#0D1A63] to-[#1b2f8a] border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:from-[#050B30] hover:to-[#050B30] focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-[#0D1A63]/20']) }}>
    {{ $slot }}
</button>
