<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    @vite(['resources/css/app.css'])
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>
</head>
<body class="antialiased">
    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-white flex flex-col items-center justify-center p-4">
        <div class="max-w-2xl w-full bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Main Content -->
            <div class="p-8">
                <!-- 404 Illustration -->
                <div class="mb-8 text-center">
                    <div class="inline-block">
                        <div class="relative">
                            <!-- Large 404 Text -->
                            <h1 class="text-9xl font-bold text-gray-200">404</h1>
                            <!-- Overlay Icon -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-blue-600">
                                    <i class="fas fa-search text-6xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error Message -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">
                        Page Not Found
                    </h2>
                    <p class="text-lg text-gray-600">
                        Oops! The page you're looking for seems to have gone missing.
                        It might have been moved, deleted, or never existed in the first place.
                    </p>
                </div>

                <!-- Search Box -->
                <div class="max-w-md mx-auto mb-8">
                    <div class="relative">
                        <input type="text"
                               placeholder="Try searching for something else..."
                               class="w-full px-4 py-3 pl-10 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
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
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-gray-50 border-t border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Popular Pages</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('products.report-item') }}"
                       class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        Report Lost Item
                    </a>
                    <a href="{{ route('products.report-found-item') }}"
                       class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        Report Found Item
                    </a>
                    <a href="{{ route('products.view-items') }}"
                       class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                        <i class="fas fa-list mr-2"></i>
                        Browse Items
                    </a>
                    <a href="{{ route('products.my-reported-items') }}"
                       class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                        <i class="fas fa-user mr-2"></i>
                        My Items
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
