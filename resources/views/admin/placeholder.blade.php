@extends('layouts.admin')

@section('title', 'Placeholder')

@section('content')
    <div class="glass-card rounded-[24px] p-10 flex flex-col items-center justify-center min-h-[500px] text-center">
        <!-- Icon Container -->
        <div class="w-24 h-24 bg-[#2B8AE2]/10 rounded-full flex items-center justify-center mb-6">
            <svg class="w-12 h-12 text-[#2B8AE2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
        </div>

        <!-- Text Content -->
        <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $pageTitle }}</h2>
        <p class="text-gray-600 text-lg mb-8 max-w-md">
            This page is currently under development. <br>
            Content will be available soon.
        </p>

        <!-- Back Button -->
        <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-gradient-to-r from-[#0D1A63] to-[#1b2f8a] text-white rounded-xl font-medium hover:from-[#050B30] hover:to-[#050B30] transition-colors shadow-lg shadow-[#0D1A63]/20">
            Return to Dashboard
        </a>
    </div>
@endsection
