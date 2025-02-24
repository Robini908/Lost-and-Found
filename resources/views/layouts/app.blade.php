<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script type="text/javascript" src="../node_modules/tw-elements/dist/js/tw-elements.umd.min.js"></script>
    <link rel="stylesheet" href="app.css" />
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
    @filepondScripts
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
    @livewireScripts
</body>

</html>
