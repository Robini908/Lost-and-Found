<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button class="ms-4">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>

        <div class="mt-6">
            <div class="flex items-center justify-center">
                <span class="text-gray-600">{{ __('Or login with') }}</span>
            </div>
            <div class="flex items-center justify-center mt-4 space-x-4">
                <a href="{{ route('social.login', 'google') }}" class="p-2 bg-white border rounded-full hover:bg-gray-50 transition duration-300">
                    <img src="https://www.svgrepo.com/show/355037/google.svg" alt="Google" class="w-6 h-6">
                </a>
                <a href="{{ route('social.login', 'twitter') }}" class="p-2 bg-white border rounded-full hover:bg-gray-50 transition duration-300">
                    <img src="https://www.svgrepo.com/show/475689/twitter-color.svg" alt="Twitter" class="w-6 h-6">
                </a>
                <a href="{{ route('social.login', 'github') }}" class="p-2 bg-white border rounded-full hover:bg-gray-50 transition duration-300">
                    <img src="https://www.svgrepo.com/show/512317/github-142.svg" alt="GitHub" class="w-6 h-6">
                </a>
            </div>
        </div>
    </x-authentication-card>
</x-guest-layout>
