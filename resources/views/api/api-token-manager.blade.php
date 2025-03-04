<?php
$tokenTypes = [
    'read' => ['icon' => 'fa-eye', 'description' => 'Read-only access to your data'],
    'write' => ['icon' => 'fa-edit', 'description' => 'Create and modify your data'],
    'full' => ['icon' => 'fa-key', 'description' => 'Full access to your account'],
];
?>

<div class="space-y-8">
    <!-- Generate API Token -->
    <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-xl font-normal text-gray-900">{{ __('Create API Token') }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ __('Generate secure tokens for third-party service integration.') }}</p>
        </div>

        <form wire:submit.prevent="createApiToken" class="px-6 py-4">
            <div class="space-y-6">
            <!-- Token Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Token Name') }}</label>
                    <div class="mt-1 relative rounded-full shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-tag text-gray-400"></i>
                        </div>
                        <input type="text" id="name" wire:model="createApiTokenForm.name"
                               class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm"
                               placeholder="Enter a descriptive name for your token">
                    </div>
                <x-input-error for="name" class="mt-2" />
            </div>

                <!-- Token Type Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('Token Type') }}</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($tokenTypes as $type => $details)
                            <label class="relative block cursor-pointer">
                                <input type="radio" name="token_type" value="{{ $type }}" wire:model="createApiTokenForm.type"
                                       class="sr-only peer">
                                <div class="rounded-2xl border-2 border-gray-200 p-4 peer-checked:border-blue-500 peer-checked:ring-2 peer-checked:ring-blue-500 transition-all duration-200">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-{{ $type === 'full' ? 'purple' : ($type === 'write' ? 'green' : 'blue') }}-100 flex items-center justify-center">
                                            <i class="fas {{ $details['icon'] }} text-{{ $type === 'full' ? 'purple' : ($type === 'write' ? 'green' : 'blue') }}-600"></i>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-gray-900">{{ ucfirst($type) }} Access</h4>
                                            <p class="text-xs text-gray-500 mt-1">{{ $details['description'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

            <!-- Token Permissions -->
            @if (Laravel\Jetstream\Jetstream::hasPermissions())
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('Permissions') }}</label>
                        <div class="bg-gray-50 rounded-2xl p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach (Laravel\Jetstream\Jetstream::$permissions as $permission)
                                    <label class="inline-flex items-center">
                                        <div class="relative flex items-start">
                                            <div class="flex items-center h-5">
                                                <input type="checkbox" wire:model="createApiTokenForm.permissions" value="{{ $permission }}"
                                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            </div>
                                            <div class="ml-3">
                                                <span class="text-sm text-gray-700">{{ $permission }}</span>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Token Expiration -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('Token Expiration') }}</label>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        @foreach(['24 hours', '7 days', '30 days', 'Never'] as $expiration)
                            <label class="relative block cursor-pointer">
                                <input type="radio" name="expiration" value="{{ $expiration }}" wire:model="createApiTokenForm.expiration"
                                       class="sr-only peer">
                                <div class="rounded-xl border border-gray-200 p-3 text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all duration-200">
                                    <span class="text-sm font-medium text-gray-900">{{ $expiration }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
            <x-action-message class="me-3" on="created">
                {{ __('Created.') }}
            </x-action-message>

                <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-full shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-plus-circle mr-2"></i>
                    {{ __('Create Token') }}
                </button>
            </div>
        </form>
    </div>

    @if ($this->user->tokens->isNotEmpty())
        <!-- Manage API Tokens -->
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-xl font-normal text-gray-900">{{ __('Active API Tokens') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Manage and monitor your active API tokens.') }}</p>
            </div>

            <div class="px-6 py-4">
                <div class="divide-y divide-gray-100">
                        @foreach ($this->user->tokens->sortBy('name') as $token)
                        <div class="py-4 flex items-center justify-between">
                            <div class="flex items-center min-w-0">
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-key text-gray-500"></i>
                                </div>
                                <div class="ml-4 truncate">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $token->name }}</h4>
                                    <div class="flex items-center mt-1">
                                        @if($token->last_used_at)
                                            <span class="text-xs text-gray-500">
                                                <i class="fas fa-clock mr-1"></i>
                                            {{ __('Last used') }} {{ $token->last_used_at->diffForHumans() }}
                                            </span>
                                    @endif
                                    @if (Laravel\Jetstream\Jetstream::hasPermissions())
                                            <span class="ml-4 text-xs text-blue-600 cursor-pointer hover:text-blue-800"
                                                  wire:click="manageApiTokenPermissions({{ $token->id }})">
                                                <i class="fas fa-cog mr-1"></i>
                                            {{ __('Permissions') }}
                                            </span>
                                    @endif
                                    </div>
                                </div>
                            </div>

                            <div class="ml-4 flex items-center space-x-3">
                                <button class="text-gray-400 hover:text-gray-500" title="Copy Token ID">
                                    <i class="fas fa-copy"></i>
                                </button>
                                <button class="text-red-400 hover:text-red-500"
                                        wire:click="confirmApiTokenDeletion({{ $token->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
            </div>
        </div>
    @endif

    <!-- Token Value Modal -->
    <x-dialog-modal wire:model.live="displayingToken">
        <x-slot name="title">
            <div class="flex items-center">
                <i class="fas fa-key text-blue-500 mr-2"></i>
                {{ __('New API Token') }}
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <div class="bg-blue-50 text-blue-700 p-4 rounded-2xl text-sm">
                {{ __('Please copy your new API token. For your security, it won\'t be shown again.') }}
            </div>

                <div class="relative">
            <x-input x-ref="plaintextToken" type="text" readonly :value="$plainTextToken"
                            class="font-mono text-sm pr-24 bg-gray-50"
                autofocus autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                            @showing-token-modal.window="setTimeout(() => $refs.plaintextToken.select(), 250)" />

                    <button class="absolute right-2 top-1/2 transform -translate-y-1/2 px-3 py-1 text-sm text-blue-600 hover:text-blue-800"
                            x-data="{ copied: false }"
                            @click="
                                navigator.clipboard.writeText($refs.plaintextToken.value);
                                copied = true;
                                setTimeout(() => copied = false, 2000)
                            ">
                        <span x-show="!copied">
                            <i class="fas fa-copy mr-1"></i> Copy
                        </span>
                        <span x-show="copied" x-cloak>
                            <i class="fas fa-check mr-1"></i> Copied!
                        </span>
                    </button>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <button wire:click="$set('displayingToken', false)"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                {{ __('Close') }}
            </button>
        </x-slot>
    </x-dialog-modal>

    <!-- API Token Permissions Modal -->
    <x-dialog-modal wire:model.live="managingApiTokenPermissions">
        <x-slot name="title">
            <div class="flex items-center">
                <i class="fas fa-shield-alt text-blue-500 mr-2"></i>
            {{ __('API Token Permissions') }}
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="bg-gray-50 rounded-2xl p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach (Laravel\Jetstream\Jetstream::$permissions as $permission)
                        <label class="inline-flex items-center">
                            <div class="relative flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" wire:model="updateApiTokenForm.permissions" value="{{ $permission }}"
                                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                </div>
                                <div class="ml-3">
                                    <span class="text-sm text-gray-700">{{ $permission }}</span>
                                </div>
                            </div>
                    </label>
                @endforeach
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end space-x-3">
                <button wire:click="$set('managingApiTokenPermissions', false)"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                {{ __('Cancel') }}
                </button>

                <button wire:click="updateApiToken"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-full shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ __('Save Changes') }}
                </button>
            </div>
        </x-slot>
    </x-dialog-modal>

    <!-- Delete Token Confirmation Modal -->
    <x-dialog-modal wire:model.live="confirmingApiTokenDeletion">
        <x-slot name="title">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
            {{ __('Delete API Token') }}
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="bg-red-50 text-red-700 p-4 rounded-2xl text-sm">
                {{ __('Are you sure you want to delete this API token? This action cannot be undone.') }}
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end space-x-3">
                <button wire:click="$toggle('confirmingApiTokenDeletion')"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                {{ __('Cancel') }}
                </button>

                <button wire:click="deleteApiToken"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    {{ __('Delete Token') }}
                </button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
