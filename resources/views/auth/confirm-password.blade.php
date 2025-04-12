<x-guest-auth>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.08)] backdrop-blur-sm bg-white/90 overflow-hidden sm:rounded-3xl border border-gray-100">
            <div class="text-center mb-8">
                <a href="/" class="flex justify-center mb-6">
                    <x-authentication-card-logo />
                </a>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 text-transparent bg-clip-text">Confirm Password</h2>
                <p class="text-gray-500 mt-2">Please confirm your password before continuing</p>
            </div>

            <div class="mb-4 text-sm text-gray-600 bg-gray-50 p-4 rounded-lg border border-gray-100">
                {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
            </div>

            <x-validation-errors class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-600" />

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div>
                    <x-label for="password" value="{{ __('Password') }}" class="text-gray-700 font-medium" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-indigo-400"></i>
                        </div>
                        <x-input id="password" class="block mt-1 w-full pl-10 border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition duration-200" type="password" name="password" required autocomplete="current-password" />
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:-translate-y-0.5">
                        <i class="fas fa-shield-alt mr-2"></i>
                        {{ __('Confirm Password') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Decorative elements -->
        <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden z-[-1]">
            <div class="absolute -top-40 -left-40 w-80 h-80 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute top-0 -right-20 w-80 h-80 bg-indigo-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-40 left-20 w-80 h-80 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
        </div>
    </div>
</x-guest-auth>
