@props([
    'type',
    'points',
    'description',
    'date',
    'item' => null
])

<div class="flex items-center justify-between hover:bg-gray-50 p-4 rounded-lg transition-colors duration-200">
    <div class="flex items-center space-x-4">
        <div class="{{ $type === 'earned' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} rounded-full p-3">
            <i class="fas {{ $type === 'earned' ? 'fa-plus' : 'fa-minus' }} text-xl"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-900">{{ $description }}</p>
            <p class="text-xs text-gray-500">{{ $date }}</p>
            @if($item)
                <a href="{{ route('lost-items.show', $item->id) }}"
                   class="text-xs text-indigo-600 hover:text-indigo-800 mt-1 inline-block">
                    View Item Details
                </a>
            @endif
        </div>
    </div>
    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $type === 'earned' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
        {{ $type === 'earned' ? '+' : '' }}{{ number_format($points) }}
    </span>
</div>
