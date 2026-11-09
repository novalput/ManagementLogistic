@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="h-full flex flex-col">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Supervisor Dashboard</h2>
            <p class="text-gray-600 mt-1">Monitor warehouse activities and item status</p>
        </div>
    </div>

    <!-- 4 Key Metrics Cards - Horizontal Layout -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Items Card -->
        <a href="{{ route('items.list') }}" class="glass-card rounded-[24px] p-6 flex flex-col justify-between h-40 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg cursor-pointer block">
            <h3 class="text-gray-800 text-lg font-medium">Total Items</h3>
            <p class="text-5xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </a>

        <!-- Available Returnable Items Card -->
        <a href="{{ route('items.list') }}" class="glass-card rounded-[24px] p-6 flex flex-col justify-between h-40 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg cursor-pointer block">
            <h3 class="text-gray-800 text-lg font-medium">Available Returnable</h3>
            <p class="text-5xl font-bold text-gray-900">{{ $stats['available_returnable'] }}</p>
        </a>
            
        <!-- Available Consumable Items Card -->
        <a href="{{ route('items.list') }}" class="glass-card rounded-[24px] p-6 flex flex-col justify-between h-40 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg cursor-pointer block">
            <h3 class="text-gray-800 text-lg font-medium">Available Consumable</h3>
            <p class="text-5xl font-bold text-gray-900">{{ $stats['available_consumable'] }}</p>
        </a>

        <!-- Borrowed Items Card -->
        <a href="{{ route('reports', ['status' => 'borrowed']) }}" class="glass-card rounded-[24px] p-6 flex flex-col justify-between h-40 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg cursor-pointer block">
            <h3 class="text-gray-800 text-lg font-medium">Borrowed Items</h3>
            <p class="text-5xl font-bold text-gray-900">{{ $stats['borrowed'] ?? 0 }}</p>
        </a>
    </div>

    <!-- Recent Activity Card -->
    <div class="glass-card rounded-[24px] p-8 flex flex-col min-h-[300px]">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Recent Activity</h2>
            <a href="{{ route('reports') }}" class="text-sm text-[#2B8AE2] font-semibold hover:underline">View All</a>
        </div>
        
        <div class="flex-1 overflow-y-auto space-y-4 pr-2 custom-scrollbar">
            @forelse($recentActivities ?? [] as $activity)
                <div class="flex items-baseline gap-3">
                    <span class="text-gray-800 text-xl">•</span>
                    <p class="text-gray-700 text-lg">
                        <span class="font-medium text-gray-900">{{ $activity->item_name ?? 'Item' }}</span> 
                        {{ $activity->action ?? 'was updated' }} 
                        <span class="text-gray-500">{{ $activity->date ?? 'recently' }}</span>.
                    </p>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center h-48 text-gray-500">
                    <svg class="w-12 h-12 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500 italic">No recent activity.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
