@if(Auth::check())
<div class="user-profile-section pt-4 border-t border-gray-200 mt-6 flex items-center gap-3 flex-shrink-0 w-full" style="position: relative; z-index: 10; display: flex; visibility: visible;">
    <a href="{{ route('profile.edit') }}" class="w-10 h-10 rounded-full bg-gray-100 flex-shrink-0 overflow-hidden hover:opacity-80 transition-opacity border border-white">
        @php
            $user = Auth::user();
            $avatarUrl = $user && $user->avatar_path 
                ? asset('storage/'.$user->avatar_path) 
                : 'https://ui-avatars.com/api/?name='.urlencode($user->name ?? 'Admin User').'&background=random&color=fff';
        @endphp
        <img src="{{ $avatarUrl }}" alt="Admin" class="w-full h-full object-cover">
    </a>
    <div class="flex-1 min-w-0">
        <a href="{{ route('profile.edit') }}" class="block text-sm font-bold text-[#0D1A63] truncate hover:text-[#1b2f8a] transition-colors">
            {{ Auth::user()->name ?? 'Admin' }}
        </a>
        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email ?? 'Administrator' }}</p>
    </div>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="text-gray-400 hover:text-[#0D1A63] transition-colors flex-shrink-0" title="Log Out">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
        </button>
    </form>
</div>
@endif
