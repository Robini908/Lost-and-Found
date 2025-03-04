<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="space-y-6">
                    <!-- User Preferences -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Preferences') }}</h3>
                        <div class="mt-4 space-y-4">
                            <!-- Add user preferences here -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" class="form-checkbox" name="email_notifications">
                                    <span class="ml-2">{{ __('Receive email notifications') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Communication Settings -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Communication') }}</h3>
                        <div class="mt-4 space-y-4">
                            <!-- Add communication settings here -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" class="form-checkbox" name="sms_notifications">
                                    <span class="ml-2">{{ __('Receive SMS notifications') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="flex justify-end">
                        <x-button type="submit">
                            {{ __('Save Settings') }}
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
