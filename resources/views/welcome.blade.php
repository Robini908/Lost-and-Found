<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @livewireStyles
</head>

<body class="font-sans antialiased bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <img src="https://via.placeholder.com/40" alt="Logo" class="h-8 w-8 mr-2">
                    <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 text-transparent bg-clip-text">Lost & Found Hub</span>
                </div>
                @if (Route::has('login'))
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-gray-700 hover:text-blue-600 font-medium transition duration-150">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 font-medium transition duration-150">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition duration-150">Register</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </nav>

                <!-- Hero Section -->
    <section class="pt-24 pb-12 bg-gradient-to-b from-blue-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="text-center lg:text-left">
                    <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 leading-tight mb-6">
                        Find What's Lost, <br>
                        <span class="bg-gradient-to-r from-blue-600 to-indigo-600 text-transparent bg-clip-text">Return What's Found</span>
                    </h1>
                    <p class="text-lg text-gray-600 mb-8">Our AI-powered platform connects people who have lost items with those who have found them, making the recovery process simple and efficient.</p>
                    <div class="flex flex-col sm:flex-row justify-center lg:justify-start space-y-4 sm:space-y-0 sm:space-x-4">
                        <a href="{{ route('products.report-item') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition duration-150 shadow-lg hover:shadow-xl">
                            <i class="fas fa-search mr-2"></i>
                            Report Lost Item
                        </a>
                        <a href="{{ route('products.report-found-item') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 transition duration-150 shadow-lg hover:shadow-xl">
                            <i class="fas fa-hand-holding-heart mr-2"></i>
                            Report Found Item
                        </a>
                    </div>
                </div>
                <div class="hidden lg:block">
                    <img src="https://via.placeholder.com/600x400" alt="Hero" class="rounded-lg shadow-2xl">
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-blue-50 rounded-xl p-6 text-center">
                    <div class="text-4xl font-bold text-blue-600 mb-2">95%</div>
                    <div class="text-gray-600">Success Rate</div>
                </div>
                <div class="bg-green-50 rounded-xl p-6 text-center">
                    <div class="text-4xl font-bold text-green-600 mb-2">24h</div>
                    <div class="text-gray-600">Average Recovery Time</div>
                </div>
                <div class="bg-purple-50 rounded-xl p-6 text-center">
                    <div class="text-4xl font-bold text-purple-600 mb-2">5000+</div>
                    <div class="text-gray-600">Items Recovered</div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-16 bg-gray-50" x-data="{ step: 1 }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12">How It Works</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="relative" @mouseenter="step = 1">
                    <div class="bg-white rounded-lg shadow-lg p-6 h-full transform transition duration-300 hover:scale-105"
                         :class="{ 'ring-2 ring-blue-500': step === 1 }">
                        <div class="text-center mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto">
                                <i class="fas fa-upload text-2xl text-blue-600"></i>
                            </div>
                        </div>
                        <h3 class="text-xl font-semibold text-center mb-4">Report Item</h3>
                        <p class="text-gray-600 text-center">Submit details about your lost item or an item you've found, including photos and location information.</p>
                        </div>
                    </div>

                <!-- Step 2 -->
                <div class="relative" @mouseenter="step = 2">
                    <div class="bg-white rounded-lg shadow-lg p-6 h-full transform transition duration-300 hover:scale-105"
                         :class="{ 'ring-2 ring-blue-500': step === 2 }">
                        <div class="text-center mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                                <i class="fas fa-magic text-2xl text-green-600"></i>
                            </div>
                        </div>
                        <h3 class="text-xl font-semibold text-center mb-4">AI Matching</h3>
                        <p class="text-gray-600 text-center">Our AI system automatically matches lost items with found items using image recognition and location data.</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="relative" @mouseenter="step = 3">
                    <div class="bg-white rounded-lg shadow-lg p-6 h-full transform transition duration-300 hover:scale-105"
                         :class="{ 'ring-2 ring-blue-500': step === 3 }">
                        <div class="text-center mb-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto">
                                <i class="fas fa-handshake text-2xl text-purple-600"></i>
                        </div>
                        </div>
                        <h3 class="text-xl font-semibold text-center mb-4">Connect & Recover</h3>
                        <p class="text-gray-600 text-center">Get notified when there's a match and safely connect with the finder to recover your item.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12">Why Choose Us</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="text-blue-600 mb-4">
                        <i class="fas fa-robot text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">AI-Powered Matching</h3>
                    <p class="text-gray-600">Advanced algorithms to match lost items with found ones accurately.</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="text-green-600 mb-4">
                        <i class="fas fa-shield-alt text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Secure Platform</h3>
                    <p class="text-gray-600">Your data is protected with enterprise-grade security measures.</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="text-purple-600 mb-4">
                        <i class="fas fa-bell text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Real-time Notifications</h3>
                    <p class="text-gray-600">Get instant alerts when your item is found or matched.</p>
                                </div>
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="text-red-600 mb-4">
                        <i class="fas fa-coins text-3xl"></i>
                                </div>
                    <h3 class="text-lg font-semibold mb-2">Reward System</h3>
                    <p class="text-gray-600">Earn points and rewards for helping others find their items.</p>
                                </div>
                            </div>
                        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-blue-600 to-indigo-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-6">Ready to Find Your Lost Item?</h2>
            <p class="text-xl text-blue-100 mb-8">Join thousands of users who have successfully recovered their belongings.</p>
            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-blue-600 bg-white hover:bg-gray-50 transition duration-150 shadow-lg hover:shadow-xl">
                        Go to Dashboard
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-blue-600 bg-white hover:bg-gray-50 transition duration-150 shadow-lg hover:shadow-xl">
                        Get Started
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">About Us</h3>
                    <p class="text-gray-400">Helping people recover their lost items through innovative technology and community support.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white transition duration-150">How It Works</a></li>
                        <li><a href="#" class="hover:text-white transition duration-150">Report Item</a></li>
                        <li><a href="#" class="hover:text-white transition duration-150">Success Stories</a></li>
                        <li><a href="#" class="hover:text-white transition duration-150">FAQs</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <ul class="space-y-2">
                        <li><a href="mailto:support@lostandfound.com" class="hover:text-white transition duration-150">support@lostandfound.com</a></li>
                        <li><a href="tel:+1234567890" class="hover:text-white transition duration-150">+1 (234) 567-890</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Follow Us</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition duration-150">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition duration-150">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition duration-150">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition duration-150">
                            <i class="fab fa-linkedin text-xl"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p>&copy; {{ date('Y') }} Lost & Found Hub. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @stack('modals')
    @filepondScripts
    @livewireScripts
</body>

</html>
