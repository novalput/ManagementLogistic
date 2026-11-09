@extends('layouts.admin')

@section('title', 'Dashboard')

@section('header')
    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
        <span id="greeting-text">Good Morning</span>, 
        <span>{{ Auth::user()->name ?? 'Admin' }}</span>!
    </h1>
@endsection

@section('content')
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
            <p class="text-5xl font-bold text-gray-900">{{ $stats['borrowed'] }}</p>
        </a>
    </div>

    <!-- Recent Activity Card - Full Width Below -->
    <div class="glass-card rounded-[24px] p-8 flex flex-col min-h-[300px]">
            <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Recent Activity</h2>
                <a href="{{ route('reports') }}" class="text-sm text-[#2B8AE2] font-semibold hover:underline">View All</a>
            </div>
            
        <div class="flex-1 overflow-y-auto space-y-4 pr-2 custom-scrollbar">
                @forelse($recentActivities as $activity)
                <div class="flex items-baseline gap-3">
                    <span class="text-gray-800 text-xl">•</span>
                    <p class="text-gray-700 text-lg">
                        <span class="font-medium text-gray-900">{{ $activity->item_name }}</span> 
                        {{ $activity->action }} 
                        <span class="text-gray-500">{{ $activity->date }}</span>.
                    </p>
                </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-48 text-gray-500">
                        <i class="fa-regular fa-file-lines fa-3x"></i>
                        <p class="text-gray-500 italic">No recent activity.</p>
                    </div>
                @endforelse
        </div>
    </div>
@endsection
