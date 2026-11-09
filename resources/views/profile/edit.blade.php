@extends('layouts.admin')

@section('title', 'Profile')

@section('content')
<div class="h-full flex flex-col overflow-y-auto">

    <!-- Status Messages -->
    @if (session('status'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-3 sm:px-4 py-3 rounded-lg sm:rounded-xl relative mb-4 mx-2 sm:mx-0 text-sm" role="alert">
            <span class="block sm:inline">{{ session('status') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-3 sm:px-4 py-3 rounded-lg sm:rounded-xl relative mb-4 mx-2 sm:mx-0 text-sm" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- 
        LAYOUT STRUCTURE:
        Full width content that fills the available space
    -->
    <div class="w-full h-full px-0 sm:px-2 md:px-4 py-2 sm:py-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            
            <!-- LEFT CARD: Profile & Edit Form -->
            <div class="glass-card rounded-2xl sm:rounded-[24px] p-4 sm:p-6 flex flex-col border-2 border-white/50 shadow-xl">
                <!-- Profile Header -->
                <div class="flex flex-col items-center mb-6 sm:mb-8">
                    <!-- Avatar with gradient ring -->
                    <div class="relative mb-3 sm:mb-4">
                        <div class="w-20 h-20 sm:w-28 sm:h-28 rounded-full p-1 bg-gradient-to-br from-[#2B8AE2] to-[#5CB3FF] shadow-lg">
                            <div class="w-full h-full rounded-full border-2 sm:border-4 border-white bg-gray-200 overflow-hidden">
                                <img
                                    id="avatarImg"
                                    src="{{ $user->avatar_path ? asset('storage/'.$user->avatar_path) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random&size=128' }}"
                                    alt="{{ $user->name }}"
                                    class="w-full h-full object-cover"
                                >
                            </div>
                        </div>
                        <!-- Edit Avatar Button -->
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'change-photo-modal')" class="absolute bottom-0 right-0 sm:bottom-1 sm:right-1 bg-white rounded-full p-1.5 sm:p-2 border border-gray-200 shadow-md text-gray-600 hover:text-[#2B8AE2] hover:bg-gray-50 transition-all hover:scale-110" title="Change Avatar">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Name & Role -->
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-2 text-center px-2">{{ $user->name }}</h2>
                    <span class="px-2.5 sm:px-3 py-1 text-[10px] sm:text-xs rounded-full font-bold bg-gradient-to-r from-[#2B8AE2] to-[#5CB3FF] text-white shadow-sm uppercase tracking-wider">
                        {{ $user->role }}
                    </span>
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-200/50 mb-4 sm:mb-6"></div>

                <!-- Edit Profile Form -->
                <form method="post" action="{{ route('profile.update') }}" class="space-y-4 sm:space-y-5 flex-1 flex flex-col">
                    @csrf
                    @method('patch')

                    <div>
                        <label class="block text-[10px] sm:text-xs font-bold text-gray-600 mb-1.5 sm:mb-2 ml-1 uppercase tracking-wide">Full Name</label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            class="w-full rounded-lg sm:rounded-xl text-sm py-2.5 sm:py-3 px-3 sm:px-4 border border-gray-200 focus:border-[#2B8AE2] focus:ring-2 focus:ring-[#2B8AE2]/20 bg-white/70 transition-all"
                            required
                        >
                        <x-input-error class="mt-1" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <label class="block text-[10px] sm:text-xs font-bold text-gray-600 mb-1.5 sm:mb-2 ml-1 uppercase tracking-wide">Email Address</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            class="w-full rounded-lg sm:rounded-xl text-sm py-2.5 sm:py-3 px-3 sm:px-4 border border-gray-200 focus:border-[#2B8AE2] focus:ring-2 focus:ring-[#2B8AE2]/20 bg-white/70 transition-all"
                            required
                        >
                        <x-input-error class="mt-1" :messages="$errors->get('email')" />
                    </div>

                    <div x-data="{ 
                        open: false,
                        showNewDivisionForm: false,
                        selectedDivision: '{{ (old('division', $user->division) && strtolower(old('division', $user->division)) !== 'other') ? old('division', $user->division) : '' }}',
                        newDivision: '',
                        divisions: ['Engineering', 'Mechanical'],
                        selectDivision(division) {
                            this.selectedDivision = division;
                            this.open = false;
                            this.showNewDivisionForm = false;
                        },
                        addNewDivision() {
                            if (this.newDivision.trim()) {
                                this.divisions.push(this.newDivision.trim());
                                this.selectedDivision = this.newDivision.trim();
                                this.newDivision = '';
                                this.showNewDivisionForm = false;
                                this.open = false;
                            }
                        },
                        cancelNewDivision() {
                            this.showNewDivisionForm = false;
                            this.newDivision = '';
                        },
                        get displayText() {
                            return this.selectedDivision || 'Select Division';
                        },
                        get hasSelection() {
                            return this.selectedDivision && this.selectedDivision.toLowerCase() !== 'other';
                        }
                    }" class="relative">
                        <label class="block text-[10px] sm:text-xs font-bold text-gray-600 mb-1.5 sm:mb-2 ml-1 uppercase tracking-wide">Division</label>
                        
                        <!-- Hidden input for form submission -->
                        <input type="hidden" name="division" :value="selectedDivision">
                        
                        <!-- Custom Dropdown Button -->
                        <button 
                            type="button"
                            @click="open = !open"
                            class="w-full rounded-lg sm:rounded-xl text-sm py-2.5 sm:py-3 px-3 sm:px-4 border border-gray-200 focus:border-[#2B8AE2] focus:ring-2 focus:ring-[#2B8AE2]/20 bg-white/70 transition-all text-left flex items-center justify-between"
                        >
                            <span :class="hasSelection ? 'text-gray-900' : 'text-gray-500'" x-text="displayText"></span>
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div 
                            x-show="open" 
                            @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2"
                            class="absolute z-50 w-full mt-2 bg-white rounded-xl border border-gray-200 shadow-lg overflow-hidden flex flex-col"
                        >
                            <!-- Scrollable Division Options -->
                            <div 
                                @wheel.stop
                                class="max-h-[200px] overflow-y-auto overscroll-contain custom-scrollbar"
                                style="scroll-behavior: smooth;"
                            >
                                <template x-for="division in divisions" :key="division">
                                    <div 
                                        @click="selectDivision(division)"
                                        class="px-4 py-3 text-sm cursor-pointer transition-colors"
                                        :class="selectedDivision === division ? 'bg-gray-500 text-white' : 'text-gray-700 hover:bg-gray-50'"
                                        x-text="division"
                                    ></div>
                                </template>
                            </div>
                            
                            <!-- Add New Division Option (Fixed Footer) -->
                            <div 
                                @click="showNewDivisionForm = true; open = false"
                                class="px-4 py-3 text-sm text-[#2B8AE2] font-semibold cursor-pointer hover:bg-gray-50 border-t border-gray-100 flex items-center gap-2 flex-shrink-0"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add New Division
                            </div>
                        </div>

                        <!-- New Division Input Form -->
                        <div x-show="showNewDivisionForm" x-transition class="mt-4">
                            <label class="block text-xs font-bold text-gray-600 mb-2 ml-1 uppercase tracking-wide">
                                New Division Name <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <input 
                                    type="text"
                                    x-model="newDivision"
                                    @keydown.enter.prevent="addNewDivision()"
                                    placeholder="Enter new division name"
                                    class="flex-1 rounded-xl text-sm py-3 px-4 border border-gray-200 focus:border-[#2B8AE2] focus:ring-2 focus:ring-[#2B8AE2]/20 bg-white/70 transition-all"
                                >
                                <button 
                                    type="button"
                                    @click="addNewDivision()"
                                    class="px-5 py-3 bg-gradient-to-r from-[#0D1A63] to-[#1b2f8a] text-white text-sm font-bold rounded-xl hover:from-[#050B30] hover:to-[#050B30] transition-colors shadow-sm"
                                >
                                    Add
                                </button>
                                <button 
                                    type="button"
                                    @click="cancelNewDivision()"
                                    class="px-5 py-3 bg-gray-200 text-gray-700 text-sm font-bold rounded-xl hover:bg-gray-300 transition-colors"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>
                        
                        <x-input-error class="mt-1" :messages="$errors->get('division')" />
                    </div>

                    <!-- Save Button -->
                    <div class="pt-3 sm:pt-4 mt-auto">
                        <button
                            type="submit"
                            class="w-full py-2.5 sm:py-3 text-xs sm:text-sm rounded-lg sm:rounded-xl font-bold bg-gradient-to-r from-[#0D1A63] to-[#1b2f8a] text-white hover:from-[#050B30] hover:to-[#050B30] transition-all shadow-lg shadow-[#0D1A63]/20 hover:shadow-xl hover:shadow-[#0D1A63]/30 transform hover:-translate-y-0.5"
                        >
                            SAVE CHANGES
                        </button>
                    </div>
                </form>
            </div>

            <!-- RIGHT CARD: Security Settings -->
            <div class="glass-card rounded-2xl sm:rounded-[24px] p-4 sm:p-6 flex flex-col border-2 border-white/50 shadow-xl">
                <!-- Header -->
                <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-gray-100 flex items-center justify-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900">Security</h3>
                        <p class="text-[10px] sm:text-xs text-gray-500">Manage your account security</p>
                    </div>
                </div>

                <!-- Security Options -->
                <div class="space-y-3 sm:space-y-4 flex-1">
                    <!-- Change Password -->
                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'update-password-modal')" class="w-full bg-white hover:bg-gray-50 border border-gray-200 hover:border-gray-300 p-3 sm:p-5 rounded-lg sm:rounded-xl transition-all shadow-sm hover:shadow-md group text-left flex items-center gap-2 sm:gap-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors flex-shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-xs sm:text-sm font-bold text-gray-900 block truncate">Change Password</span>
                            <span class="text-[10px] sm:text-xs text-gray-500 hidden sm:block">Update your password for better security</span>
                        </div>
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 group-hover:text-gray-600 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>

                    <!-- Two-Factor Auth (Optional placeholder for future) -->
                    <div class="bg-gray-50/50 border border-gray-100 p-3 sm:p-5 rounded-lg sm:rounded-xl text-left flex items-center gap-2 sm:gap-4 opacity-60">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-xs sm:text-sm font-bold text-gray-500 block truncate">Two-Factor Authentication</span>
                            <span class="text-[10px] sm:text-xs text-gray-400 hidden sm:block">Coming soon</span>
                        </div>
                        <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 text-[8px] sm:text-[10px] font-bold rounded-full bg-gray-200 text-gray-500 uppercase flex-shrink-0">Soon</span>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-200/50">
                    <p class="text-[10px] sm:text-xs font-bold text-red-600 mb-2 sm:mb-3 uppercase tracking-wide">Danger Zone</p>
                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="w-full bg-red-50 hover:bg-red-100 border border-red-100 hover:border-red-200 p-3 sm:p-4 rounded-lg sm:rounded-xl transition-all shadow-sm hover:shadow-md group text-left flex items-center gap-2 sm:gap-4">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-red-100 flex items-center justify-center group-hover:bg-red-200 transition-colors flex-shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-xs sm:text-sm font-bold text-red-700 block truncate">Delete Account</span>
                            <span class="text-[10px] sm:text-xs text-red-500 hidden sm:block">Permanently delete your account and data</span>
                        </div>
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-400 group-hover:text-red-600 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- Modals -->
    <!-- Update Password Modal -->
    <x-modal name="update-password-modal" focusable>
        <div class="p-5 sm:p-8">
            <div class="flex items-center justify-between mb-4 sm:mb-6">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">
                {{ __('Update Password') }}
            </h2>
                <button x-on:click="$dispatch('close')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="flex flex-col items-center text-center mb-4 sm:mb-6">
                <!-- Icon -->
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 rounded-full flex items-center justify-center mb-3 sm:mb-4 ring-4 ring-blue-50/50">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                </div>
                
                <p class="text-xs sm:text-sm text-gray-500 max-w-sm">
                    {{ __('Ensure your account is using a long, random password to stay secure.') }}
                </p>
            </div>


            <form method="post" action="{{ route('password.update') }}" class="w-full text-left space-y-3 sm:space-y-4">
                @csrf
                @method('put')
                
                <div>
                    <label class="block text-[10px] sm:text-xs font-semibold text-gray-700 mb-1 ml-1">Current Password</label>
                    <x-text-input id="update_password_current_password" name="current_password" type="password" class="block w-full bg-gray-50 border-gray-300 focus:border-[#2B8AE2] focus:ring-[#2B8AE2] focus:bg-white rounded-full px-3 sm:px-4 py-2 sm:py-2.5 text-sm" autocomplete="current-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
                </div>

                <div>
                    <label class="block text-[10px] sm:text-xs font-semibold text-gray-700 mb-1 ml-1">New Password</label>
                    <x-text-input id="update_password_password" name="password" type="password" class="block w-full bg-gray-50 border-gray-300 focus:border-[#2B8AE2] focus:ring-[#2B8AE2] focus:bg-white rounded-full px-3 sm:px-4 py-2 sm:py-2.5 text-sm" autocomplete="new-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
                </div>

                <div>
                    <label class="block text-[10px] sm:text-xs font-semibold text-gray-700 mb-1 ml-1">Confirm Password</label>
                    <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="block w-full bg-gray-50 border-gray-300 focus:border-[#2B8AE2] focus:ring-[#2B8AE2] focus:bg-white rounded-full px-3 sm:px-4 py-2 sm:py-2.5 text-sm" autocomplete="new-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
                </div>

                <div class="flex items-center gap-2 sm:gap-3 mt-4 sm:mt-6 pt-2">
                    <button type="button" x-on:click="$dispatch('close')" class="flex-1 py-2.5 sm:py-3.5 px-3 sm:px-4 bg-white border border-gray-200 text-gray-700 rounded-full text-sm font-bold hover:bg-gray-50 transition shadow-sm">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="flex-1 py-2.5 sm:py-3.5 px-3 sm:px-4 bg-gradient-to-r from-[#0D1A63] to-[#1b2f8a] text-white rounded-full text-sm font-bold hover:from-[#050B30] hover:to-[#050B30] transition shadow-md shadow-[#0D1A63]/20">
                        {{ __('Save') }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Delete Account Modal -->
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-5 sm:p-8">
            @csrf
            @method('delete')

            <div class="flex items-center justify-between mb-4 sm:mb-6">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">
                    {{ __('Delete Account') }}
                </h2>
                <button type="button" x-on:click="$dispatch('close')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="flex flex-col items-center text-center mb-4 sm:mb-6">
                <!-- Icon -->
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-100 rounded-full flex items-center justify-center mb-3 sm:mb-4 ring-4 ring-red-50">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>

                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">
                    {{ __('Are you sure you want to delete your account?') }}
                </h3>

                <p class="text-xs sm:text-sm text-gray-500 max-w-sm">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </p>
            </div>

            <div class="w-full text-left space-y-3 sm:space-y-4">
                <div>
                    <label for="password" class="block text-[10px] sm:text-xs font-semibold text-gray-700 mb-1 ml-1">{{ __('Password') }}</label>
                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        class="block w-full bg-gray-50 border-gray-300 focus:border-[#2B8AE2] focus:ring-[#2B8AE2] focus:bg-white rounded-full px-3 sm:px-4 py-2 sm:py-2.5 text-sm"
                        placeholder="{{ __('Password') }}"
                    />
                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1" />
                </div>

                <div class="flex items-center gap-2 sm:gap-3 mt-4 sm:mt-6 pt-2">
                    <button type="button" x-on:click="$dispatch('close')" class="flex-1 py-2.5 sm:py-3.5 px-3 sm:px-4 bg-white border border-gray-200 text-gray-700 rounded-full text-sm font-bold hover:bg-gray-50 transition shadow-sm">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="flex-1 py-2.5 sm:py-3.5 px-3 sm:px-4 bg-red-600 text-white rounded-full text-sm font-bold hover:bg-red-700 transition shadow-md shadow-red-200">
                        {{ __('Delete Account') }}
                    </button>
                </div>
            </div>
        </form>
    </x-modal>

    <!-- Change Photo Modal -->
    @include('profile.partials.change-photo-modal')

</div>

@endsection
