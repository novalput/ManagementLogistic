@extends('layouts.admin')

@section('title', 'Return')

@section('header')
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Returning Page</h1>
        <h2 class="text-sm md:text-base text-gray-600 font-medium mt-1">Process and audit returned equipment from borrowers</h2>
    </div>
@endsection

@section('content')
    <div class="flex items-center justify-center h-full">
        <div class="glass-card rounded-[24px] p-8 w-full max-w-xl">
            <h3 class="text-center text-xl font-semibold text-gray-900 mb-6">Identification</h3>
            <form method="POST" action="{{ route('return.verify') }}" class="space-y-4">
                @csrf
                <label class="block">
                    <span class="text-sm font-medium text-gray-700">Borrowing ID</span>
                    <input type="text" name="borrowing_id" value="{{ old('borrowing_id') }}"
                           placeholder="Enter borrowing ID"
                           class="mt-2 w-full px-4 py-2 rounded-xl border-gray-200 focus:border-[#2B8AE2] focus:ring-[#2B8AE2] bg-white/70">
                </label>
                @error('borrowing_id')
                    <div class="text-sm text-red-600">{{ $message }}</div>
                @enderror
                <p class="text-xs text-gray-500">enter the unique ID on the borrowing slip</p>
                <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-[#0D1A63] to-[#1b2f8a] text-white rounded-xl font-medium hover:from-[#050B30] hover:to-[#050B30] transition-colors">
                    Return These Items
                </button>
            </form>
        </div>
    </div>
@endsection

