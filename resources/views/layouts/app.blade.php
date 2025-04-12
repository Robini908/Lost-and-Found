<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data
      :class="{ 'dark': $store.darkMode.on }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Font Awesome Pro Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Your existing scripts -->
    <script type="text/javascript" src="../node_modules/tw-elements/dist/js/tw-elements.umd.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- reCAPTCHA Script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @filepondScripts

    @livewireChartsScripts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Flag Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css"/>
</head>

<body class="font-sans antialiased alpine-initializing"
      x-data
      x-init="() => {
          document.body.classList.remove('alpine-initializing');
          document.body.classList.add('alpine-ready');
      }">

    <livewire:toasts />
    <x-banner />

    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <!-- Navigation Menu with x-cloak -->
        <div x-cloak>
            @livewire('navigation-menu')
        </div>

        @if (isset($header))
            <header class="bg-white shadow-sm glass-effect" x-cloak>
                <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <div class="gradient-text">
                        {{ $header }}
                    </div>
                    <nav class="flex items-center space-x-2">
                        @php
                            $segments = request()->segments();
                            $url = '';
                        @endphp
                        @foreach ($segments as $segment)
                            @php
                                if (is_numeric($segment)) {
                                    $segment = substr(md5($segment), 0, 8);
                                }
                                $url .= '/' . $segment;
                            @endphp
                            <a href="{{ url($url) }}"
                               class="btn-hover-effect text-sm text-gray-600 hover:text-gray-900 transition-colors duration-200">
                                {{ ucfirst($segment) }}
                            </a>
                            @if (!$loop->last)
                                <span class="text-gray-400">
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </span>
                            @endif
                        @endforeach
                    </nav>
                </div>
            </header>
        @endif

        <!-- Sidebar with x-cloak -->
        <div x-cloak>
            @php
                use App\Services\RoleService;
                $roleService = app(RoleService::class);
            @endphp
            @include('partials.sidebar', ['user' => auth()->user(), 'roleService' => $roleService])
        </div>

        <!-- Page Content -->
        <main class="py-6">
            <div data-aos="fade-up" data-aos-duration="800">
                {{ $slot }}
            </div>
        </main>
    </div>

    @stack('modals')
    @livewireScripts
    @stack('scripts')
</body>

</html>
