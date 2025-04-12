<x-guest-auth>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.08)] backdrop-blur-sm bg-white/90 overflow-hidden sm:rounded-3xl border border-gray-100">
            <div class="text-center mb-8">
                <a href="/" class="flex justify-center mb-6">
                    <x-authentication-card-logo />
                </a>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 text-transparent bg-clip-text">Reset Password</h2>
                <p class="text-gray-500 mt-2">Enter your email to receive a password reset link</p>
            </div>

            <div class="mb-4 text-sm text-gray-600 bg-gray-50 p-4 rounded-lg border border-gray-100">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </div>

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-4 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <x-validation-errors class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-600" />

            <form method="POST" action="{{ route('password.email') }}">
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

                <div class="mt-6">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:-translate-y-0.5">
                        <i class="fas fa-paper-plane mr-2"></i>
                        {{ __('Email Password Reset Link') }}
                    </button>
                </div>
            </form>

            <p class="mt-8 text-center text-sm text-gray-600">
                {{ __('Remember your password?') }}
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-800 transition duration-200">
                    {{ __('Sign in') }}
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
