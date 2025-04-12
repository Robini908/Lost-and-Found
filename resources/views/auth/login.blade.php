<x-guest-auth>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-gray-50 to-gray-100"
         x-data="{ loading: false }">
        <!-- Loading State -->
        <x-loading-state message="Authenticating..." />

        <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.08)] backdrop-blur-sm bg-white/90 overflow-hidden sm:rounded-3xl border border-gray-100">
            <div class="text-center mb-8">
                <a href="/" class="flex justify-center mb-6">
                    <x-authentication-card-logo />
                </a>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 text-transparent bg-clip-text">Welcome Back</h2>
                <p class="text-gray-500 mt-2">Sign in to your account to continue</p>
            </div>

            <x-validation-errors class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-600" />

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-4 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}"
                  @submit="loading = true">
                @csrf

                <div>
                    <x-label for="email" value="{{ __('Email') }}" class="text-gray-700 font-medium" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-indigo-400"></i>
                        </div>
                        <x-input id="email" class="block mt-1 w-full pl-10 border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition duration-200" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    </div>
                </div>

                <div class="mt-4">
                    <x-label for="password" value="{{ __('Password') }}" class="text-gray-700 font-medium" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-indigo-400"></i>
                        </div>
                        <x-input id="password" class="block mt-1 w-full pl-10 border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition duration-200" type="password" name="password" required autocomplete="current-password" />
                    </div>
                </div>

                <div class="flex items-center justify-between mt-4">
                    <label for="remember_me" class="flex items-center">
                        <x-checkbox id="remember_me" name="remember" class="rounded text-indigo-600 focus:ring-indigo-500" />
                        <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a class="text-sm text-indigo-600 hover:text-indigo-800 transition duration-200" href="{{ route('password.request') }}">
                            {{ __('Forgot password?') }}
                        </a>
                    @endif
                </div>

                <div class="mt-6">
                    <div class="bg-gray-50 rounded-xl p-4 shadow-sm border border-gray-100">
                        <div class="mb-2">
                            <h3 class="text-sm font-medium text-gray-700">Security Check</h3>
                            <p class="text-xs text-gray-500">Quick verification to keep your account safe</p>
                        </div>
                        <x-recaptcha />
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:-translate-y-0.5">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        {{ __('Sign in') }}
                    </button>
                </div>
            </form>

            <div class="mt-8">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">{{ __('Or continue with') }}</span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-3 gap-3">
                    <a href="{{ route('social.login', 'google') }}" class="flex justify-center py-2 px-4 border border-gray-200 rounded-xl shadow-sm bg-white hover:bg-gray-50 transition-all duration-200 transform hover:-translate-y-0.5">
                        <img src="https://www.svgrepo.com/show/355037/google.svg" alt="Google" class="h-5 w-5">
                    </a>
                    <a href="{{ route('social.login', 'twitter') }}" class="flex justify-center py-2 px-4 border border-gray-200 rounded-xl shadow-sm bg-white hover:bg-gray-50 transition-all duration-200 transform hover:-translate-y-0.5">
                        <img src="https://www.svgrepo.com/show/475689/twitter-color.svg" alt="Twitter" class="h-5 w-5">
                    </a>
                    <a href="{{ route('social.login', 'github') }}" class="flex justify-center py-2 px-4 border border-gray-200 rounded-xl shadow-sm bg-white hover:bg-gray-50 transition-all duration-200 transform hover:-translate-y-0.5">
                        <img src="https://www.svgrepo.com/show/512317/github-142.svg" alt="GitHub" class="h-5 w-5">
                    </a>
                </div>
            </div>

            <p class="mt-8 text-center text-sm text-gray-600">
                {{ __('Don\'t have an account?') }}
                <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-800 transition duration-200">
                    {{ __('Sign up') }}
                </a>
            </p>
        </div>

        <!-- Decorative elements -->
        <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden z-[-1]">
            <div class="absolute -top-40 -left-40 w-80 h-80 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute top-0 -right-20 w-80 h-80 bg-indigo-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-40 left-20 w-80 h-80 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
        </div>
    </div>
</x-guest-auth>
