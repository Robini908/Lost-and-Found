<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @livewireStyles
</head>

<body class="font-sans antialiased bg-gradient-to-r from-blue-100 to-indigo-100 text-gray-800">
    <div class="min-h-screen flex flex-col items-center justify-center">
        <div class="w-full max-w-7xl px-6">
            <!-- Header -->
            <header class="flex justify-between items-center py-10">
                <div class="flex items-center space-x-4">
                    <img src="https://via.placeholder.com/50" alt="Logo" class="h-12 w-12">
                    <h1 class="text-2xl font-bold">Lost and Found Hub</h1>
                </div>
                @if (Route::has('login'))
                    <nav class="flex space-x-4">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="text-lg font-medium text-gray-700 hover:text-gray-900">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-lg font-medium text-gray-700 hover:text-gray-900">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="text-lg font-medium text-gray-700 hover:text-gray-900">Register</a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </header>

            <!-- Main Content -->
            <main class="mt-12">
                <!-- Hero Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Left Side: Text Content -->
                    <div class="text-center lg:text-left">
                        <h1 class="text-5xl font-bold mb-6">Welcome to Lost and Found Hub</h1>
                        <p class="text-xl text-gray-600 mb-8">Helping you find your lost items quickly and easily.</p>
                        <div class="flex justify-center lg:justify-start space-x-4">
                            <a href="#"
                                class="inline-block bg-gradient-to-r from-blue-500 to-indigo-500 text-white px-8 py-4 rounded-lg shadow-lg hover:from-blue-600 hover:to-indigo-600 transition duration-300">Report
                                Lost Item</a>
                            <a href="#"
                                class="inline-block bg-gradient-to-r from-green-500 to-lime-500 text-white px-8 py-4 rounded-lg shadow-lg hover:from-green-600 hover:to-lime-600 transition duration-300">View
                                Found Items</a>
                        </div>
                    </div>

                    <!-- Right Side: Collage Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-transparent p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                            <i class="fas fa-search text-6xl mb-4 text-blue-500"></i>
                            <h2 class="text-xl font-semibold mb-2">Report Lost Item</h2>
                            <p class="text-gray-600">Easily report your lost items and get notified when they are found.</p>
                        </div>
                        <div class="bg-transparent p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                            <i class="fas fa-box-open text-6xl mb-4 text-green-500"></i>
                            <h2 class="text-xl font-semibold mb-2">View Found Items</h2>
                            <p class="text-gray-600">Browse through the list of found items and claim yours.</p>
                        </div>
                        <div class=" bg-transparent col-span-2  p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                            <i class="fas fa-users text-6xl mb-4 text-yellow-500"></i>
                            <h2 class="text-xl font-semibold mb-2">Community Support</h2>
                            <p class="text-gray-600">Join our community and help others find their lost items.</p>
                        </div>
                    </div>
                </div>

                <!-- Lost Items Carousel -->
                <div class="mt-12">
                    <livewire:lost-items-carousel lazy/>
                </div>

                <!-- Playful Animation Section -->
                <div class="mt-16">
                    <h2 class="text-3xl font-bold mb-8 text-center">How It Works</h2>
                    <div class="flex justify-center">
                        <div class="w-full max-w-4xl  p-8 rounded-lg shadow-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-search text-6xl mb-4 text-blue-500"></i>
                                    <p class="text-lg font-semibold">Report</p>
                                </div>
                                <i class="fas fa-arrow-right text-4xl text-gray-500"></i>
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-box-open text-6xl mb-4 text-green-500"></i>
                                    <p class="text-lg font-semibold">Find</p>
                                </div>
                                <i class="fas fa-arrow-right text-4xl text-gray-500"></i>
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-users text-6xl mb-4 text-yellow-500"></i>
                                    <p class="text-lg font-semibold">Reunite</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="mt-16 py-8 text-center text-sm text-gray-600">
                Lost and Found Hub &copy; {{ date('Y') }}. All rights reserved.
            </footer>
        </div>
    </div>
    @stack('modals')
    @filepondScripts
    @livewireScripts
</body>

</html>
