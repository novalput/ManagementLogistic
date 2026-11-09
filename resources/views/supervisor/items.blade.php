@extends('layouts.admin')

@section('title', 'Items')

@section('content')
<div class="h-full flex flex-col">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Items Monitoring</h2>
            <p class="text-gray-600 mt-1">View and add notes to items</p>
        </div>
    </div>

    <!-- Items List -->
    <div class="glass-card rounded-[24px] p-8 flex flex-col">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">All Items</h2>
        </div>
        
        <div class="flex-1 overflow-y-auto space-y-4 pr-2 custom-scrollbar">
            <p class="text-gray-500 italic">Items monitoring and notes feature coming soon...</p>
        </div>
    </div>
</div>
@endsection
