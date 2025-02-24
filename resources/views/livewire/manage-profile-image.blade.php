<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-indigo-50 to-white px-4">
    <div class="bg-white shadow-xl rounded-2xl p-6 w-full max-w-md text-center">
        <!-- Header with User Info -->
        <div class="mb-4">
            <h2 class="text-xl font-bold text-gray-800">{{ Auth::user()->name }}</h2>
            <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
        </div>

        <div x-data="{ file: null, changing: false }" class="flex flex-col items-center">
            <!-- Image Display Section -->
            <div class="relative flex items-center justify-center space-x-4 transition-all duration-300"
                 :class="changing ? 'flex-row' : 'flex-col'">

                <!-- Current Image (Moves to Side When Changing) -->
                @if (Auth::user()->profile_photo_path)
                    <div class="relative transition-all duration-300 group"
                         :class="changing ? 'w-20 h-20' : 'w-32 h-32'" @click="$refs.fileInput.click()">
                        <img src="{{ Storage::url(Auth::user()->profile_photo_path) }}"
                             class="rounded-full object-cover border-4 border-gray-200 shadow-md">
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 bg-black bg-opacity-50 text-white text-xs rounded-full">
                            Click to change
                        </div>
                    </div>
                @endif

                <!-- Image Upload Placeholder (Only Visible When Changing) -->
                <template x-if="changing">
                    <div class="relative w-32 h-32 cursor-pointer" @click="$refs.fileInput.click()">
                        <img x-bind:src="file ? URL.createObjectURL(file) : null"
                             class="rounded-full object-cover border-4 border-blue-400 shadow-md"
                             x-show="file">
                        <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 text-white text-sm rounded-full"
                             x-show="!file">
                            Tap to upload
                        </div>
                    </div>
                </template>

                <!-- Default Profile Image Placeholder -->
                <template x-if="!changing && !file">
                    <div class="border-2 border-dashed border-gray-300 rounded-full p-6 flex flex-col items-center justify-center cursor-pointer"
                         @click="changing = true; $refs.fileInput.click()">
                        <span class="text-gray-500 text-sm">Tap to change</span>
                    </div>
                </template>
            </div>

            <!-- Hidden File Input -->
            <input type="file" id="profileImage" wire:model="profileImage" class="hidden" x-ref="fileInput"
                   @change="file = $event.target.files[0]; changing = true">

            <!-- Actions -->
            <div class="mt-8">
                <template x-if="changing">
                    <span class="text-sm text-gray-500 cursor-pointer hover:text-green-500"
                          wire:click="saveProfileImage" @click="changing = false">
                        Tap to save
                    </span>
                </template>

                @if (Auth::user()->profile_photo_path)
                    <span class="mt-2 text-sm text-gray-500 cursor-pointer hover:text-red-500"
                          wire:click="deleteProfileImage">
                        Tap to remove
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
