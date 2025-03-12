<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Enhanced Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Your existing scripts and styles -->
    <script type="text/javascript" src="../node_modules/tw-elements/dist/js/tw-elements.umd.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="app.css" />
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.min.js"></script>

    <!-- reCAPTCHA Script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>


    @livewireChartsScripts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Custom Font Styles -->
    <style>
        :root {
            --font-primary: 'Poppins', sans-serif;
            --font-secondary: 'Inter', sans-serif;
            --font-accent: 'Montserrat', sans-serif;
        }

        body {
            font-family: var(--font-primary);
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-accent);
        }

        .font-secondary {
            font-family: var(--font-secondary);
        }

        .font-accent {
            font-family: var(--font-accent);
        }

        [x-cloak] { display: none !important; }
    </style>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Flag Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css"/>
</head>

<body class="font-sans antialiased">

    <livewire:toasts />
    <x-banner />

    <div class="min-h-screen bg-gray-50">
        @livewire('navigation-menu')
        @if (isset($header))
            <header class="bg-gray-100 shadow">
                <div class="max-w-7xl mx-auto py-2 px-4 sm:px-3 lg:px-8 flex justify-between items-center">
                    {{ $header }}
                    <nav>
                        @php
                            $segments = request()->segments();
                            $url = '';
                        @endphp
                        @foreach ($segments as $segment)
                            @php
                                // Hash the segment if it is numeric (assuming it's an ID)
                                if (is_numeric($segment)) {
                                    $segment = substr(md5($segment), 0, 8); // Short hash
                                }
                                $url .= '/' . $segment;
                            @endphp
                            <a href="{{ url($url) }}" class="ml-4 text-gray-700 hover:text-gray-900">
                                {{ ucfirst($segment) }}
                            </a>
                            @if (!$loop->last)
                                <span class="mx-2 text-gray-500">></span>
                            @endif
                        @endforeach
                    </nav>
                </div>
            </header>
        @endif

        <!-- Include Sidebar with User and RoleService -->
        @php
            use App\Services\RoleService;
            $roleService = app(RoleService::class);
        @endphp
        @include('partials.sidebar', ['user' => auth()->user(), 'roleService' => $roleService])

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    @stack('modals')
    @filepondScripts
    @livewireScripts
    @stack('scripts')

</body>

</html>
