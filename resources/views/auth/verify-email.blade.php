<x-guest-auth>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.08)] backdrop-blur-sm bg-white/90 overflow-hidden sm:rounded-3xl border border-gray-100">
            <div class="text-center mb-8">
                <a href="/" class="flex justify-center mb-6">
                    <x-authentication-card-logo />
                </a>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 text-transparent bg-clip-text">Verify Email</h2>
                <p class="text-gray-500 mt-2">Please verify your email address to continue</p>
            </div>

            <div class="mb-4 text-sm text-gray-600 bg-gray-50 p-4 rounded-lg border border-gray-100">
                {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-4 rounded-lg">
                    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                </div>
            @endif

            <div class="mt-6 flex flex-col space-y-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf

                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:-translate-y-0.5">
                        <i class="fas fa-envelope mr-2"></i>
                        {{ __('Resend Verification Email') }}
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf

                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-gray-200 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:-translate-y-0.5">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Decorative elements -->
        <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden z-[-1]">
            <div class="absolute -top-40 -left-40 w-80 h-80 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute top-0 -right-20 w-80 h-80 bg-indigo-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-40 left-20 w-80 h-80 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
        </div>
    </div>
</x-guest-auth>
