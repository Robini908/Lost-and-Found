<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-b from-blue-50 to-white">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-2xl overflow-hidden sm:rounded-2xl">
            <div class="text-center mb-8">
                <a href="/" class="flex justify-center mb-4">
                    <x-authentication-card-logo />
                </a>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 text-transparent bg-clip-text">Set New Password</h2>
                <p class="text-gray-600 mt-2">Create a strong password for your account</p>
            </div>

            <x-validation-errors class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-600" />

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <x-label for="email" value="{{ __('Email') }}" class="text-gray-700" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <x-input id="email" class="block mt-1 w-full pl-10 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm transition duration-150" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                    </div>
                </div>

                <div class="mt-4">
                    <x-label for="password" value="{{ __('Password') }}" class="text-gray-700" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <x-input id="password" class="block mt-1 w-full pl-10 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm transition duration-150" type="password" name="password" required autocomplete="new-password" />
                    </div>
                </div>

                <div class="mt-4">
                    <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" class="text-gray-700" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <x-input id="password_confirmation" class="block mt-1 w-full pl-10 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm transition duration-150" type="password" name="password_confirmation" required autocomplete="new-password" />
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                        <i class="fas fa-key mr-2"></i>
                        {{ __('Reset Password') }}
                    </button>
                </div>
            </form>

            <p class="mt-8 text-center text-sm text-gray-600">
                {{ __('Remember your password?') }}
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-800 transition duration-150">
                    {{ __('Sign in') }}
                </a>
            </p>
        </div>
    </div>
</x-guest-layout>
