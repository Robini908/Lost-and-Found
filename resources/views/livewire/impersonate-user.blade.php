<div>
    <button
        wire:click="openModal"
        class="text-gray-500 hover:text-gray-700 transition-colors duration-200"
        title="Impersonate User">
        <i class="fas fa-user-secret text-lg"></i>
    </button>

    <x-modal name="impersonate-modal" :show="$showModal" focusable>
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Impersonate User') }}
                </h2>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            @if($isImpersonating)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                {{ __('You are currently impersonating another user.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <button wire:click="stopImpersonating" class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-user-times mr-2"></i>
                    {{ __('Stop Impersonating') }}
                </button>
            @else
                <div class="mb-4">
                    <x-input
                        type="text"
                        class="w-full"
                        placeholder="{{ __('Search users...') }}"
                        wire:model.live="search"
                    />
                </div>

                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @forelse($this->users as $user)
                        <div class="flex items-center justify-between p-3 bg-white border rounded-lg hover:bg-gray-50 transition-colors duration-150">
                            <div class="flex items-center space-x-3">
                                @if($user->profile_photo_url)
                                    <img src="{{ $user->profile_photo_url }}" class="h-8 w-8 rounded-full object-cover" alt="{{ $user->name }}">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 flex items-center justify-center text-white font-medium">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </div>
                            </div>
                            <button
                                wire:click="impersonate({{ $user->id }})"
                                class="inline-flex items-center px-3 py-1 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 active:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                <i class="fas fa-user-secret mr-2"></i>
                                {{ __('Impersonate') }}
                            </button>
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-500">
                            {{ __('No users found.') }}
                        </div>
                    @endforelse
                </div>

                @if($this->users->hasPages())
                    <div class="mt-4">
                        {{ $this->users->links() }}
                    </div>
                @endif
            @endif
        </div>
    </x-modal>
</div>
