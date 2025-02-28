@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-100">
        <!-- Search Header -->
        <div class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-semibold text-gray-900">
                        {{ $header ?? 'Search' }}
                    </h1>
                    <div class="w-full max-w-xs">
                        <livewire:global-search />
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{ $slot }}
        </div>
    </div>
@endsection
