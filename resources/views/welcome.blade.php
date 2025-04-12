<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Modern UI Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Manrope:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @livewireStyles
    @push('styles')
    <style>
        /* Modern UI Animations */
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }

        @keyframes pulse-glow {
            0% { box-shadow: 0 0 5px rgba(99, 102, 241, 0.3), 0 0 10px rgba(99, 102, 241, 0.2); }
            50% { box-shadow: 0 0 20px rgba(99, 102, 241, 0.6), 0 0 30px rgba(99, 102, 241, 0.4); }
            100% { box-shadow: 0 0 5px rgba(99, 102, 241, 0.3), 0 0 10px rgba(99, 102, 241, 0.2); }
        }

        @keyframes shimmer {
            0% { background-position: -100% 0; }
            100% { background-position: 200% 0; }
        }

        /* Animation Classes */
        .animate-blob {
            animation: blob 7s infinite;
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }

        .animate-shimmer {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            background-size: 200% 100%;
            animation: shimmer 2s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }

        /* Modern UI Elements */
        .glass-card {
            backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
        }

        .neo-button {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .neo-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: all 0.5s ease;
        }

        .neo-button:hover::before {
            left: 100%;
        }

        /* Futuristic Patterns */
        .cyber-grid {
            background-image:
                linear-gradient(rgba(99, 102, 241, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99, 102, 241, 0.05) 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .dot-pattern {
            background-image: radial-gradient(rgba(99, 102, 241, 0.2) 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .wave-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='20' viewBox='0 0 100 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M21.184 20c.357-.13.72-.264 1.088-.402l1.768-.661C33.64 15.347 39.647 14 50 14c10.271 0 15.362 1.222 24.629 4.928.955.383 1.869.74 2.75 1.072h6.225c-2.51-.73-5.139-1.691-8.233-2.928C65.888 13.278 60.562 12 50 12c-10.626 0-16.855 1.397-26.66 5.063l-1.767.662c-2.475.923-4.66 1.674-6.724 2.275h6.335zm0-20C13.258 2.892 8.077 4 0 4V2c5.744 0 9.951-.574 14.85-2h6.334zM77.38 0C85.239 2.966 90.502 4 100 4V2c-6.842 0-11.386-.542-16.396-2h-6.225zM0 14c8.44 0 13.718-1.21 22.272-4.402l1.768-.661C33.64 5.347 39.647 4 50 4c10.271 0 15.362 1.222 24.629 4.928C84.112 12.722 89.438 14 100 14v-2c-10.271 0-15.362-1.222-24.629-4.928C65.888 3.278 60.562 2 50 2 39.374 2 33.145 3.397 23.34 7.063l-1.767.662C13.223 10.84 8.163 12 0 12v2z' fill='%236366f1' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #6366f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #4f46e5;
        }

        /* Font Styles */
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            font-weight: 400;
            letter-spacing: -0.01em;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', 'Manrope', sans-serif;
            letter-spacing: -0.03em;
            font-weight: 700;
        }

        .font-heading {
            font-family: 'Outfit', 'Manrope', sans-serif;
        }

        .font-body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        }

        .font-display {
            font-family: 'Manrope', 'Outfit', sans-serif;
            letter-spacing: -0.04em;
        }

        /* Fix for hero image */
        .hero-image-container {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
            border-radius: 1rem;
        }

        .hero-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
    </style>
    @endpush
</head>

<body class="font-sans antialiased bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white/80 backdrop-blur-md shadow-sm fixed w-full z-50 transition-all duration-300"
         x-data="{ scrolled: false }"
         @scroll.window="scrolled = (window.pageYOffset > 20)"
         :class="{ 'py-2 bg-white/90 shadow-md': scrolled, 'py-4': !scrolled }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 w-10 mr-3 animate-pulse-glow">
                    <span class="text-2xl font-bold bg-gradient-to-r from-indigo-600 via-blue-600 to-purple-600 text-transparent bg-clip-text">Lost & Found Hub</span>
                </div>
                @if (Route::has('login'))
                    <div class="flex items-center space-x-6">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="neo-button group relative inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 transition-all duration-300 shadow-lg">
                                <span class="absolute -inset-0.5 bg-indigo-600 blur opacity-0 group-hover:opacity-30 transition-all duration-300 rounded-lg"></span>
                                <span class="relative flex items-center">
                                    <i class="fas fa-tachometer-alt mr-2"></i>
                                    Dashboard
                                </span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 font-medium transition duration-300">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                   class="neo-button group relative inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 transition-all duration-300 shadow-lg">
                                    <span class="absolute -inset-0.5 bg-indigo-600 blur opacity-0 group-hover:opacity-30 transition-all duration-300 rounded-lg"></span>
                                    <span class="relative">Register</span>
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section with Animation -->
    <section class="relative pt-32 pb-20 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 cyber-grid"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-indigo-50/80 via-white/90 to-white"></div>

        <!-- Animated Blobs -->
        <div class="absolute top-20 left-10 w-72 h-72 bg-indigo-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute top-40 right-10 w-72 h-72 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-20 left-1/2 transform -translate-x-1/2 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="text-center lg:text-left"
                     x-data="{ shown: false }"
                     x-intersect="shown = true">
                    <div class="inline-block mb-3 px-4 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-semibold animate-pulse-glow">
                        <span class="flex items-center font-body">
                            <i class="fas fa-star mr-2 text-indigo-500"></i>
                            AI-Powered Lost & Found Platform
                        </span>
                    </div>
                    <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight mb-6"
                        x-show="shown"
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 transform -translate-y-8"
                        x-transition:enter-end="opacity-100 transform translate-y-0">
                        Find What's Lost, <br>
                        <span class="bg-gradient-to-r from-indigo-600 via-blue-600 to-purple-600 text-transparent bg-clip-text">Return What's Found</span>
                    </h1>
                    <p class="font-body text-lg md:text-xl text-gray-600 mb-8 max-w-xl mx-auto lg:mx-0 leading-relaxed">Our AI-powered platform connects people who have lost items with those who have found them, making the recovery process simple and efficient.</p>
                    <div class="flex flex-col sm:flex-row justify-center lg:justify-start space-y-4 sm:space-y-0 sm:space-x-4">
                        <a href="{{ url('/report-item') }}" class="neo-button inline-flex items-center justify-center px-6 py-4 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl">
                            <i class="fas fa-search mr-2"></i>
                            Report Lost Item
                        </a>
                        <a href="{{ url('/report-found-item') }}" class="neo-button inline-flex items-center justify-center px-6 py-4 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 transition-all duration-300 shadow-lg hover:shadow-xl">
                            <i class="fas fa-hand-holding-heart mr-2"></i>
                            Report Found Item
                        </a>
                    </div>

                    <!-- Trust Badges -->
                    <div class="mt-10 flex flex-wrap justify-center lg:justify-start gap-6">
                        <div class="flex items-center text-sm text-gray-500 font-body">
                            <i class="fas fa-shield-alt text-indigo-500 mr-2"></i>
                            Secure & Private
                        </div>
                        <div class="flex items-center text-sm text-gray-500 font-body">
                            <i class="fas fa-bolt text-indigo-500 mr-2"></i>
                            Fast Matching
                        </div>
                        <div class="flex items-center text-sm text-gray-500 font-body">
                            <i class="fas fa-users text-indigo-500 mr-2"></i>
                            10K+ Users
                        </div>
                    </div>
                </div>

                <!-- Hero Image with Animation -->
                <div class="hidden lg:block relative">
                    <div class="absolute inset-0 bg-indigo-500 rounded-2xl transform rotate-3 scale-105 opacity-10"></div>
                    <div class="glass-card p-2 rounded-2xl shadow-2xl animate-float">
                        <div class="hero-image-container">
                    <img src="{{ asset('images/hero.jpg') }}" alt="Hero"
                                class="hero-image transform transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl">
                        </div>

                        <!-- Floating Elements -->
                        <div class="absolute -top-6 -right-6 glass-card p-4 rounded-lg shadow-lg animate-float animation-delay-2000">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <span class="text-sm font-medium text-gray-800">AI Matching Active</span>
                            </div>
                        </div>

                        <div class="absolute -bottom-6 -left-6 glass-card p-4 rounded-lg shadow-lg animate-float animation-delay-4000">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-map-marker-alt text-red-500"></i>
                                <span class="text-sm font-medium text-gray-800">Location Tracking</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scroll Indicator -->
            <div class="absolute bottom-5 left-1/2 transform -translate-x-1/2 animate-bounce hidden md:block">
                <a href="#features" class="text-indigo-600 hover:text-indigo-800 transition-colors duration-300">
                    <i class="fas fa-chevron-down text-xl"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Section with Dynamic Data -->
    <section id="features" class="relative py-24 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 wave-pattern opacity-50"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-white via-indigo-50/50 to-white/90"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <div class="inline-block mb-3 px-4 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-semibold">
                    <span class="flex items-center justify-center font-body">
                        <i class="fas fa-chart-line mr-2 text-purple-500"></i>
                        Real-Time Statistics
                    </span>
                </div>
                <h2 class="font-display text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Making a <span class="bg-gradient-to-r from-indigo-600 to-purple-600 text-transparent bg-clip-text">Real Impact</span>
                </h2>
                <p class="font-body text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed">
                    Our community is growing stronger every day, helping people reconnect with their lost belongings
                </p>
            </div>

            <!-- Stats Component -->
            <div class="relative">
                <!-- Glowing Orb Effects -->
                <div class="absolute -top-20 -left-20 w-64 h-64 bg-indigo-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
                <div class="absolute -bottom-20 left-20 w-64 h-64 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>

                <!-- Livewire Stats Component -->
                <livewire:landing-page-stats />
            </div>

            <!-- Trust Indicators -->
            <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center transform transition-all duration-300 hover:scale-105">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center shadow-lg animate-pulse-glow">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Secure Platform</h3>
                    <p class="text-gray-600">End-to-end encrypted data protection for all users</p>
                </div>
                <div class="text-center transform transition-all duration-300 hover:scale-105">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-lg animate-pulse-glow animation-delay-2000">
                        <i class="fas fa-bolt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Fast Matching</h3>
                    <p class="text-gray-600">AI-powered item matching with 95% accuracy rate</p>
                </div>
                <div class="text-center transform transition-all duration-300 hover:scale-105">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center shadow-lg animate-pulse-glow">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Active Community</h3>
                    <p class="text-gray-600">Growing global network of helpful users</p>
                </div>
                <div class="text-center transform transition-all duration-300 hover:scale-105">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center shadow-lg animate-pulse-glow animation-delay-4000">
                        <i class="fas fa-headset text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">24/7 Support</h3>
                    <p class="text-gray-600">Always available to help with any questions</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="relative py-24 overflow-hidden" x-data="{ step: 1 }">
        <!-- Background Pattern -->
        <div class="absolute inset-0 dot-pattern opacity-50"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-white via-indigo-50/30 to-white"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="text-center mb-16">
                <div class="inline-block mb-3 px-4 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">
                    <span class="flex items-center justify-center font-body">
                        <i class="fas fa-magic mr-2 text-blue-500"></i>
                        Simple Process
                    </span>
                </div>
                <h2 class="font-display text-3xl md:text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
                <p class="font-body text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed">
                    Our platform makes it easy to report and recover lost items through a simple three-step process
                </p>
            </div>

            <!-- Process Steps -->
            <div class="relative">
                <!-- Connection Lines -->
                <div class="hidden lg:block absolute top-1/2 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 via-blue-500 to-purple-500 transform -translate-y-1/2 z-0"></div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-12 lg:gap-8">
                <!-- Step 1 -->
                    <div class="relative z-10" @mouseenter="step = 1">
                        <div class="glass-card p-8 rounded-2xl shadow-xl backdrop-blur-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2"
                             :class="{ 'ring-2 ring-indigo-500 bg-white/30': step === 1 }">
                            <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 w-12 h-12 rounded-full bg-gradient-to-r from-indigo-600 to-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">1</div>
                            <div class="text-center mt-4 mb-6">
                                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-indigo-100 flex items-center justify-center animate-float">
                                    <i class="fas fa-upload text-3xl text-indigo-600"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-4">Report Item</h3>
                                <p class="text-gray-600">Submit details about your lost item or an item you've found, including photos and location information.</p>
                            </div>
                            <div class="mt-6 flex justify-center">
                                <a href="{{ url('/report-item') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
                                    Learn more
                                    <i class="fas fa-arrow-right ml-2 text-sm"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                <!-- Step 2 -->
                    <div class="relative z-10" @mouseenter="step = 2">
                        <div class="glass-card p-8 rounded-2xl shadow-xl backdrop-blur-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2"
                             :class="{ 'ring-2 ring-blue-500 bg-white/30': step === 2 }">
                            <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 w-12 h-12 rounded-full bg-gradient-to-r from-blue-600 to-teal-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">2</div>
                            <div class="text-center mt-4 mb-6">
                                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-blue-100 flex items-center justify-center animate-float animation-delay-2000">
                                    <i class="fas fa-robot text-3xl text-blue-600"></i>
                                </div>
                                <h3 class="font-heading text-xl font-semibold mb-3 text-gray-900">AI-Powered Matching</h3>
                                <p class="font-body text-gray-600 leading-relaxed">Advanced algorithms match lost items with found ones with 95% accuracy using image recognition and location data.</p>
                            </div>
                            <div class="mt-6 flex justify-center">
                                <a href="{{ url('/how-it-works') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                                    Learn more
                                    <i class="fas fa-arrow-right ml-2 text-sm"></i>
                                </a>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                    <div class="relative z-10" @mouseenter="step = 3">
                        <div class="glass-card p-8 rounded-2xl shadow-xl backdrop-blur-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2"
                             :class="{ 'ring-2 ring-purple-500 bg-white/30': step === 3 }">
                            <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 w-12 h-12 rounded-full bg-gradient-to-r from-purple-600 to-pink-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">3</div>
                            <div class="text-center mt-4 mb-6">
                                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-purple-100 flex items-center justify-center animate-float animation-delay-4000">
                                    <i class="fas fa-handshake text-3xl text-purple-600"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-4 bg-gradient-to-r from-purple-600 to-pink-600 text-transparent bg-clip-text">Connect & Recover</h3>
                                <p class="text-gray-600">Get notified when there's a match and safely connect with the finder to recover your item.</p>
                            </div>
                            <div class="mt-6 flex justify-center">
                                <a href="{{ url('/success-stories') }}" class="inline-flex items-center text-purple-600 hover:text-purple-800 font-medium">
                                    See success stories
                                    <i class="fas fa-arrow-right ml-2 text-sm"></i>
                                </a>
                        </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Process Indicator -->
            <div class="flex justify-center mt-12 md:hidden">
                <button @click="step = 1" :class="{ 'bg-indigo-600': step === 1, 'bg-gray-300': step !== 1 }" class="w-3 h-3 rounded-full mx-1"></button>
                <button @click="step = 2" :class="{ 'bg-blue-600': step === 2, 'bg-gray-300': step !== 2 }" class="w-3 h-3 rounded-full mx-1"></button>
                <button @click="step = 3" :class="{ 'bg-purple-600': step === 3, 'bg-gray-300': step !== 3 }" class="w-3 h-3 rounded-full mx-1"></button>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="relative py-24 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 cyber-grid"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-50/80 via-white/90 to-white/80"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="text-center mb-16">
                <div class="inline-block mb-3 px-4 py-1 bg-teal-100 text-teal-700 rounded-full text-sm font-semibold">
                    <span class="flex items-center justify-center font-body">
                        <i class="fas fa-gem mr-2 text-teal-500"></i>
                        Premium Features
                    </span>
                </div>
                <h2 class="font-display text-3xl md:text-4xl font-bold text-gray-900 mb-4">Why Choose Us</h2>
                <p class="font-body text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed">
                    Our platform offers cutting-edge technology and user-friendly features to help you find your lost items
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature 1 -->
                <div class="glass-card p-8 rounded-2xl shadow-lg backdrop-blur-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-white/20">
                    <div class="relative mb-6">
                        <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg blur opacity-25"></div>
                        <div class="relative w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-robot text-2xl text-white"></i>
                        </div>
                    </div>
                    <h3 class="font-heading text-xl font-semibold mb-3 text-gray-900">AI-Powered Matching</h3>
                    <p class="font-body text-gray-600 leading-relaxed">Advanced algorithms match lost items with found ones with 95% accuracy using image recognition and location data.</p>
                    <div class="mt-6">
                        <a href="#" class="font-body text-indigo-600 hover:text-indigo-800 font-medium text-sm inline-flex items-center">
                            Learn more
                            <i class="fas fa-chevron-right ml-1 text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="glass-card p-8 rounded-2xl shadow-lg backdrop-blur-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-white/20">
                    <div class="relative mb-6">
                        <div class="absolute -inset-1 bg-gradient-to-r from-green-600 to-teal-600 rounded-lg blur opacity-25"></div>
                        <div class="relative w-14 h-14 bg-gradient-to-br from-green-500 to-teal-600 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-shield-alt text-2xl text-white"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">Secure Platform</h3>
                    <p class="text-gray-600">Your data is protected with enterprise-grade security measures and end-to-end encryption for all communications.</p>
                    <div class="mt-6">
                        <a href="#" class="text-teal-600 hover:text-teal-800 font-medium text-sm inline-flex items-center">
                            Learn more
                            <i class="fas fa-chevron-right ml-1 text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="glass-card p-8 rounded-2xl shadow-lg backdrop-blur-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-white/20">
                    <div class="relative mb-6">
                        <div class="absolute -inset-1 bg-gradient-to-r from-purple-600 to-pink-600 rounded-lg blur opacity-25"></div>
                        <div class="relative w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-bell text-2xl text-white"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">Real-time Notifications</h3>
                    <p class="text-gray-600">Get instant alerts when your item is found or matched through email, SMS, or push notifications.</p>
                    <div class="mt-6">
                        <a href="#" class="text-purple-600 hover:text-purple-800 font-medium text-sm inline-flex items-center">
                            Learn more
                            <i class="fas fa-chevron-right ml-1 text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="glass-card p-8 rounded-2xl shadow-lg backdrop-blur-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border border-white/20">
                    <div class="relative mb-6">
                        <div class="absolute -inset-1 bg-gradient-to-r from-amber-500 to-red-500 rounded-lg blur opacity-25"></div>
                        <div class="relative w-14 h-14 bg-gradient-to-br from-amber-500 to-red-500 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-coins text-2xl text-white"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">Reward System</h3>
                    <p class="text-gray-600">Earn points and rewards for helping others find their items, redeemable for premium features and partner offers.</p>
                    <div class="mt-6">
                        <a href="#" class="text-amber-600 hover:text-amber-800 font-medium text-sm inline-flex items-center">
                            Learn more
                            <i class="fas fa-chevron-right ml-1 text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Additional Features -->
            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mr-4">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-indigo-600"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-2 text-gray-900">Location Tracking</h4>
                        <p class="text-gray-600">Precise location data helps narrow down where items were lost or found.</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex-shrink-0 mr-4">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-comments text-blue-600"></i>
                                </div>
                                </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-2 text-gray-900">Secure Messaging</h4>
                        <p class="text-gray-600">Built-in messaging system for safe communication between users.</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex-shrink-0 mr-4">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="fas fa-mobile-alt text-green-600"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-2 text-gray-900">Mobile App</h4>
                        <p class="text-gray-600">Access all features on the go with our iOS and Android applications.</p>
                    </div>
                                </div>
                            </div>
                        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-24 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 via-blue-600 to-purple-600"></div>
        <div class="absolute inset-0 opacity-10 dot-pattern"></div>

        <!-- Animated Elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden">
            <div class="absolute -top-10 -left-10 w-40 h-40 bg-white rounded-full mix-blend-overlay filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute top-1/2 -right-10 w-40 h-40 bg-white rounded-full mix-blend-overlay filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-10 left-1/3 w-40 h-40 bg-white rounded-full mix-blend-overlay filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="glass-card p-12 rounded-3xl backdrop-blur-lg border border-white/20 shadow-2xl">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="text-center lg:text-left">
                        <h2 class="font-display text-3xl md:text-4xl font-bold text-white mb-6">Ready to Find Your Lost Item?</h2>
                        <p class="font-body text-xl text-indigo-100 mb-8 leading-relaxed">Join thousands of users who have successfully recovered their belongings using our AI-powered platform.</p>
                        <div class="flex flex-col sm:flex-row justify-center lg:justify-start space-y-4 sm:space-y-0 sm:space-x-4">
                @auth
                                <a href="{{ url('/dashboard') }}" class="neo-button inline-flex items-center justify-center px-6 py-4 border border-transparent text-base font-medium rounded-xl text-indigo-600 bg-white hover:bg-gray-50 transition-all duration-300 shadow-lg hover:shadow-xl">
                                    <i class="fas fa-tachometer-alt mr-2"></i>
                        Go to Dashboard
                    </a>
                @else
                                <a href="{{ route('register') }}" class="neo-button inline-flex items-center justify-center px-6 py-4 border border-transparent text-base font-medium rounded-xl text-indigo-600 bg-white hover:bg-gray-50 transition-all duration-300 shadow-lg hover:shadow-xl">
                                    <i class="fas fa-user-plus mr-2"></i>
                        Get Started
                                </a>
                                <a href="{{ route('login') }}" class="neo-button inline-flex items-center justify-center px-6 py-4 border border-white/30 text-base font-medium rounded-xl text-white hover:bg-white/10 transition-all duration-300">
                                    <i class="fas fa-sign-in-alt mr-2"></i>
                                    Log In
                    </a>
                @endauth
                        </div>

                        <!-- Trust Badges -->
                        <div class="mt-10 flex flex-wrap justify-center lg:justify-start gap-6">
                            <div class="flex items-center text-sm text-white/80 font-body">
                                <i class="fas fa-check-circle text-white mr-2"></i>
                                Free to join
                            </div>
                            <div class="flex items-center text-sm text-white/80 font-body">
                                <i class="fas fa-check-circle text-white mr-2"></i>
                                No credit card required
                            </div>
                            <div class="flex items-center text-sm text-white/80 font-body">
                                <i class="fas fa-check-circle text-white mr-2"></i>
                                Cancel anytime
                            </div>
                        </div>
                    </div>

                    <!-- Decorative Image -->
                    <div class="hidden lg:block relative">
                        <div class="absolute inset-0 bg-white rounded-2xl transform rotate-3 scale-105 opacity-10"></div>
                        <img src="{{ asset('images/cta-image.jpg') }}" alt="Happy user" class="relative rounded-2xl shadow-2xl animate-float">

                        <!-- Floating Elements -->
                        <div class="absolute -top-6 -right-6 glass-card p-4 rounded-lg shadow-lg animate-float animation-delay-2000">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-star text-yellow-400"></i>
                                <span class="text-sm font-medium text-white">4.9/5 Rating</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="relative bg-gray-900 text-gray-300 pt-20 pb-10 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5 cyber-grid"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <!-- Footer Top -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 mb-16">
                <!-- Brand Column -->
                <div class="lg:col-span-2">
                    <div class="flex items-center mb-6">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 w-10 mr-3">
                        <span class="font-display text-2xl font-bold bg-gradient-to-r from-indigo-400 via-blue-400 to-purple-400 text-transparent bg-clip-text">Lost & Found Hub</span>
                    </div>
                    <p class="font-body text-gray-400 mb-6 max-w-md leading-relaxed">Helping people recover their lost items through innovative technology and community support. Our AI-powered platform makes finding lost items easier than ever.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-indigo-600 hover:text-white transition-all duration-300">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-blue-600 hover:text-white transition-all duration-300">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-pink-600 hover:text-white transition-all duration-300">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-blue-700 hover:text-white transition-all duration-300">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="font-heading text-lg font-semibold mb-6 text-white">Quick Links</h3>
                    <ul class="space-y-4">
                        <li>
                            <a href="{{ url('/how-it-works') }}" class="font-body text-gray-400 hover:text-white transition duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-indigo-400"></i>
                                How It Works
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/report-item') }}" class="font-body text-gray-400 hover:text-white transition duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-indigo-400"></i>
                                Report Item
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/success-stories') }}" class="font-body text-gray-400 hover:text-white transition duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-indigo-400"></i>
                                Success Stories
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/faqs') }}" class="font-body text-gray-400 hover:text-white transition duration-300 flex items-center">
                                <i class="fas fa-chevron-right text-xs mr-2 text-indigo-400"></i>
                                FAQs
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="font-heading text-lg font-semibold mb-6 text-white">Contact</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i class="fas fa-envelope text-indigo-400 mt-1 mr-3"></i>
                            <a href="mailto:support@lostandfound.com" class="font-body text-gray-400 hover:text-white transition duration-300">support@lostandfound.com</a>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone-alt text-indigo-400 mt-1 mr-3"></i>
                            <a href="tel:+1234567890" class="font-body text-gray-400 hover:text-white transition duration-300">+1 (234) 567-890</a>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt text-indigo-400 mt-1 mr-3"></i>
                            <span class="font-body text-gray-400">123 Main Street, New York, NY 10001</span>
                        </li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h3 class="font-heading text-lg font-semibold mb-6 text-white">Newsletter</h3>
                    <p class="font-body text-gray-400 mb-4">Subscribe to our newsletter for the latest updates and features.</p>
                    <form class="flex">
                        <input type="email" placeholder="Your email" class="font-body px-4 py-2 rounded-l-lg bg-gray-800 border-gray-700 text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 flex-grow">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-r-lg transition duration-300">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="font-body text-gray-500 mb-4 md:mb-0">&copy; {{ date('Y') }} Lost & Found Hub. All rights reserved.</p>
                <div class="flex space-x-6">
                    <a href="{{ url('/terms') }}" class="font-body text-gray-500 hover:text-white transition duration-300">Terms of Service</a>
                    <a href="{{ url('/privacy-policy') }}" class="font-body text-gray-500 hover:text-white transition duration-300">Privacy Policy</a>
                    <a href="{{ url('/cookies') }}" class="font-body text-gray-500 hover:text-white transition duration-300">Cookies</a>
                </div>
            </div>
        </div>
    </footer>

    @stack('modals')
    @filepondScripts
    @livewireScripts
</body>

</html>
