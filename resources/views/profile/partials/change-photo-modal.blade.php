<x-modal name="change-photo-modal" focusable>
    <div class="p-5 sm:p-8 flex flex-col items-center text-center" x-data="{ uploading: false }">
        <!-- Icon -->
        <div class="mb-4 sm:mb-6">
            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-blue-500 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
            </svg>
        </div>

        <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">
            Change Profile Photo
        </h2>

        <p class="text-xs sm:text-sm text-gray-500 mb-4 sm:mb-6 max-w-sm">
            Upload a new profile photo or remove your current one.
        </p>

        <div class="w-full space-y-2 sm:space-y-3">
            <!-- Upload Photo Form -->
            <form 
                id="avatarUploadForm" 
                method="POST" 
                action="{{ route('profile.avatar.update') }}" 
                enctype="multipart/form-data"
                x-ref="avatarForm"
            >
                @csrf
                <input 
                    type="file" 
                    id="avatarFileInput" 
                    name="avatar" 
                    accept="image/*" 
                    class="hidden"
                    x-ref="avatarInput"
                    @change="if($refs.avatarInput.files.length > 0) { uploading = true; $refs.avatarForm.submit(); }"
                >
                <button 
                    type="button" 
                    @click="$refs.avatarInput.click()"
                    :disabled="uploading"
                    class="w-full py-2.5 sm:py-3.5 px-3 sm:px-4 text-center text-sm font-bold text-gray-900 bg-white border border-gray-300 rounded-full hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span x-show="!uploading">Upload Photo</span>
                    <span x-show="uploading" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-4 w-4 sm:h-5 sm:w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Uploading...
                    </span>
                </button>
            </form>

            <!-- Remove Current Photo Button -->
            <form method="POST" action="{{ route('profile.avatar.remove') }}" x-ref="removeForm">
                @csrf
                @method('DELETE')
                <button 
                    type="button" 
                    @click="if(confirm('Are you sure you want to remove your current photo?')) { $refs.removeForm.submit(); }"
                    class="w-full py-2.5 sm:py-3.5 px-3 sm:px-4 text-center text-sm font-bold text-red-600 bg-white border border-gray-300 rounded-full hover:bg-gray-50 transition-colors"
                >
                    Remove Current Photo
                </button>
            </form>
        </div>

        <!-- Cancel Button -->
        <div class="flex items-center gap-2 sm:gap-3 mt-4 sm:mt-6 pt-2 w-full">
            <button type="button" x-on:click="$dispatch('close')" class="flex-1 py-2.5 sm:py-3.5 px-3 sm:px-4 bg-white border border-gray-200 text-gray-700 rounded-full text-sm font-bold hover:bg-gray-50 transition shadow-sm">
                Cancel
            </button>
        </div>
    </div>
</x-modal>