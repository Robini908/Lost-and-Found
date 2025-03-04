<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Error') - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    @vite(['resources/css/app.css'])
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>
</head>
<body class="antialiased">
    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-white flex flex-col items-center justify-center p-4">
        <div class="max-w-2xl w-full bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="p-8 text-center">
                <!-- Error Icon -->
                <div class="mb-8 inline-flex items-center justify-center w-16 h-16 rounded-full @yield('icon-bg', 'bg-red-100') @yield('icon-color', 'text-red-600')">
                    <i class="fas @yield('icon', 'fa-exclamation-circle') text-3xl"></i>
                </div>

                <!-- Error Message -->
                <h1 class="text-3xl font-bold text-gray-900 mb-4">
                    @yield('message')
                </h1>
                <p class="text-lg text-gray-600 mb-8">
                    @yield('description')
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @section('actions')
                        <a href="{{ url()->previous() }}"
                           class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Go Back
                        </a>
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <i class="fas fa-home mr-2"></i>
                            Return Home
                        </a>
                    @show
                </div>
            </div>

            <!-- Additional Content -->
            @yield('additional-content')

            <!-- Help Section -->
            <div class="bg-gray-50 border-t border-gray-100 p-6">
                <h2 class="text-sm font-semibold text-gray-900 mb-2">Need Help?</h2>
                <p class="text-sm text-gray-600">
                    If you need assistance, please contact our support team:
                </p>
                <div class="mt-3 flex items-center justify-center space-x-4">
                    <a href="mailto:support@example.com" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                        <i class="fas fa-envelope mr-2"></i>
                        Email Support
                    </a>
                    <span class="text-gray-300">|</span>
                    <a href="#" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                        <i class="fas fa-question-circle mr-2"></i>
                        Help Center
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-sm text-gray-500">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
