<div x-data="{ open: false }" class="relative">
    <!-- Toggle Button -->
    <button @click="open = !open" class="bg-gray-500 text-white p-2 rounded-full hover:bg-gray-600 transition" data-tippy-content="Impersonate User">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">

            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
    </button>

    <!-- Card -->
    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-white border border-gray-300 rounded-md shadow-lg overflow-hidden z-10">
        @if (Auth::user()->isImpersonating())
            <!-- Only show Stop Impersonation Button -->
            <div class="sticky bottom-0 bg-white p-4 border-t border-gray-200">
                <button
                    wire:click="stopImpersonation"
                    class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition w-full"
                >
                    Stop Impersonation
                </button>
            </div>
        @else
            <!-- Show the rest of the card content when not impersonating -->
            <div class="sticky top-0 bg-white p-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Impersonate User</h3>
            </div>

            <!-- Search Box -->
            <div class="p-4 border-b border-gray-200">
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Search by name or email..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
            </div>

            <!-- User List -->
            <div class="max-h-96 overflow-y-auto p-4">
                @if ($users->isEmpty())
                    <p class="text-gray-500 text-center">No users found.</p>
                @else
                    @foreach ($users as $user)
                        <div
                            wire:click="selectUser({{ $user->id }})"
                            class="cursor-pointer p-3 hover:bg-gray-100 rounded-md transition flex items-center justify-between {{ $selectedUserId === $user->id ? 'bg-green-50' : '' }}"
                        >
                            <div>
                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </div>
                            @if ($selectedUserId === $user->id)
                                <i class="fas fa-check text-green-500"></i>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Impersonate Button -->
            <div class="sticky bottom-0 bg-white p-4 border-t border-gray-200">
                <button
                    wire:click="startImpersonation"
                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition w-full"
                >
                    Impersonate
                </button>
            </div>
        @endif
    </div>
</div>
