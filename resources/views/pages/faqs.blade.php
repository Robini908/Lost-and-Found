<x-guest-layout>
    <x-slot name="title">FAQs</x-slot>

    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
        <!-- Hero Section -->
        <div class="pt-32 pb-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 text-center mb-6">
                    Frequently Asked Questions
                </h1>
                <p class="text-xl text-gray-600 text-center max-w-3xl mx-auto">
                    Find answers to common questions about our lost and found platform.
                </p>
            </div>
        </div>

        <!-- FAQs Section -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
            <div class="space-y-6">
                @foreach($faqs as $faq)
                    <div x-data="{ open: false }"
                         class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <button @click="open = !open"
                                class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50">
                            <span class="text-lg font-medium text-gray-900">
                                {{ $faq->question }}
                            </span>
                            <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                 :class="{ 'rotate-180': open }"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             class="px-6 pb-4">
                            <p class="text-gray-600">
                                {{ $faq->answer }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-guest-layout>
