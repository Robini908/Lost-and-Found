@props([
    'title',
    'value',
    'icon',
    'subtitle' => null,
    'gradientFrom' => 'from-indigo-500',
    'gradientTo' => 'to-purple-600'
])

<div {{ $attributes->merge(['class' => "bg-gradient-to-br {$gradientFrom} {$gradientTo} rounded-xl p-6 text-white shadow-lg"]) }}>
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium opacity-75">{{ $title }}</p>
            <h3 class="text-3xl font-bold mt-1">{{ $value }}</h3>
        </div>
        <div class="bg-white/20 rounded-full p-3">
            <i class="fas {{ $icon }} text-2xl"></i>
        </div>
    </div>
    @if($subtitle)
        <div class="mt-4 text-sm opacity-75">
            {{ $subtitle }}
        </div>
    @endif
</div>
