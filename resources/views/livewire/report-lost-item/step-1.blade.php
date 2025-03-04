<div class="space-y-8" x-data="{ hoveredType: null, reportType: '{{ $reportType }}' }" x-on:report-type-updated.window="reportType = $event.detail">
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-3">How would you like to report this item?</h2>
        <p class="mt-2 text-sm text-gray-600 max-w-2xl mx-auto">Choose the most appropriate option for your situation. Your selection will help us streamline the reporting process.</p>
    </div>

    <!-- Dynamic Message -->
    <div class="mb-8">
        <div class="bg-gradient-to-r transition-all duration-500"
             :class="{
                 'from-blue-50 to-blue-100 border-blue-100': reportType === 'reported',
                 'from-purple-50 to-purple-100 border-purple-100': reportType === 'searched',
                 'from-green-50 to-green-100 border-green-100': reportType === 'found',
                 'from-yellow-50 to-yellow-100 border-yellow-100': reportType === ''
             }">
            <div class="border rounded-lg p-4 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 transition-colors duration-300"
                             :class="{
                                 'text-blue-400': reportType === 'reported',
                                 'text-purple-400': reportType === 'searched',
                                 'text-green-400': reportType === 'found',
                                 'text-yellow-400 animate-pulse': reportType === ''
                             }"
                             viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                  :d="reportType === ''
                                      ? 'M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z'
                                      : 'M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z'"
                                  clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm transition-colors duration-300"
                           :class="{
                               'text-blue-700': reportType === 'reported',
                               'text-purple-700': reportType === 'searched',
                               'text-green-700': reportType === 'found',
                               'text-yellow-700': reportType === ''
                           }"
                           x-text="reportType === 'reported'
                                  ? 'You are reporting a lost item. We will help you create a detailed report to increase the chances of finding your item.'
                                  : reportType === 'searched'
                                  ? 'You are helping search for someone else\'s lost item. Thank you for being part of our community!'
                                  : reportType === 'found'
                                  ? 'You are reporting a found item. You will earn reward points for helping return items to their owners.'
                                  : 'Please select a report type to continue. This will help us process your report more effectively.'"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        <!-- Lost Item Option -->
        <div class="relative transform transition-all duration-300 hover:scale-105"
             x-on:mouseenter="hoveredType = 'reported'"
             x-on:mouseleave="hoveredType = null">
            <label class="group block h-full cursor-pointer"
                   :class="{
                       'ring-4 ring-blue-100': hoveredType === 'reported' || reportType === 'reported'
                   }">
                <input type="radio" wire:model="reportType" x-on:change="reportType = 'reported'" name="reportType" value="reported" class="sr-only peer">
                <div class="relative overflow-hidden rounded-xl border-2 transition-all duration-500"
                     :class="{
                         'border-blue-600 shadow-lg': reportType === 'reported',
                         'border-gray-200 hover:border-blue-400': reportType !== 'reported',
                         'border-blue-400 shadow-md': hoveredType === 'reported' && reportType !== 'reported',
                         'bg-gradient-to-br from-blue-50 via-white to-blue-100': reportType === 'reported',
                         'bg-white': reportType !== 'reported'
                     }">
                    <div class="p-6">
                        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-blue-100 to-blue-50 group-hover:from-blue-200 group-hover:to-blue-100 transition-all duration-300">
                            <svg class="w-8 h-8 transition-colors duration-300"
                                 :class="{
                                     'text-blue-600': reportType === 'reported' || hoveredType === 'reported',
                                     'text-blue-400': reportType !== 'reported' && hoveredType !== 'reported'
                                 }"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-semibold transition-colors duration-300"
                                :class="{
                                    'text-blue-700': reportType === 'reported' || hoveredType === 'reported',
                                    'text-gray-900': reportType !== 'reported' && hoveredType !== 'reported'
                                }">
                                I Lost an Item
                            </h3>
                            <p class="mt-2 text-sm text-gray-500">Report your own lost item and let us help you find it</p>
                        </div>
                    </div>
                    <div class="absolute top-3 right-3 transform transition-all duration-300"
                         :class="{
                             'opacity-100 scale-100': reportType === 'reported',
                             'opacity-0 scale-0': reportType !== 'reported'
                         }">
                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </label>
        </div>

        <!-- Search Item Option -->
        <div class="relative transform transition-all duration-300 hover:scale-105"
             x-on:mouseenter="hoveredType = 'searched'"
             x-on:mouseleave="hoveredType = null">
            <label class="group block h-full cursor-pointer"
                   :class="{
                       'ring-4 ring-purple-100': hoveredType === 'searched' || reportType === 'searched'
                   }">
                <input type="radio" wire:model="reportType" x-on:change="reportType = 'searched'" name="reportType" value="searched" class="sr-only peer">
                <div class="relative overflow-hidden rounded-xl border-2 transition-all duration-500"
                     :class="{
                         'border-purple-600 shadow-lg': reportType === 'searched',
                         'border-gray-200 hover:border-purple-400': reportType !== 'searched',
                         'border-purple-400 shadow-md': hoveredType === 'searched' && reportType !== 'searched',
                         'bg-gradient-to-br from-purple-50 via-white to-purple-100': reportType === 'searched',
                         'bg-white': reportType !== 'searched'
                     }">
                    <div class="p-6">
                        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-purple-100 to-purple-50 group-hover:from-purple-200 group-hover:to-purple-100 transition-all duration-300">
                            <svg class="w-8 h-8 transition-colors duration-300"
                                 :class="{
                                     'text-purple-600': reportType === 'searched' || hoveredType === 'searched',
                                     'text-purple-400': reportType !== 'searched' && hoveredType !== 'searched'
                                 }"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-semibold transition-colors duration-300"
                                :class="{
                                    'text-purple-700': reportType === 'searched' || hoveredType === 'searched',
                                    'text-gray-900': reportType !== 'searched' && hoveredType !== 'searched'
                                }">
                                Looking for Item
                            </h3>
                            <p class="mt-2 text-sm text-gray-500">Help search for someone else's lost item</p>
                        </div>
                    </div>
                    <div class="absolute top-3 right-3 transform transition-all duration-300"
                         :class="{
                             'opacity-100 scale-100': reportType === 'searched',
                             'opacity-0 scale-0': reportType !== 'searched'
                         }">
                        <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </label>
        </div>

        <!-- Found Item Option -->
        <div class="relative transform transition-all duration-300 hover:scale-105"
             x-on:mouseenter="hoveredType = 'found'"
             x-on:mouseleave="hoveredType = null">
            <label class="group block h-full cursor-pointer"
                   :class="{
                       'ring-4 ring-green-100': hoveredType === 'found' || reportType === 'found'
                   }">
                <input type="radio" wire:model="reportType" x-on:change="reportType = 'found'" name="reportType" value="found" class="sr-only peer">
                <div class="relative overflow-hidden rounded-xl border-2 transition-all duration-500"
                     :class="{
                         'border-green-600 shadow-lg': reportType === 'found',
                         'border-gray-200 hover:border-green-400': reportType !== 'found',
                         'border-green-400 shadow-md': hoveredType === 'found' && reportType !== 'found',
                         'bg-gradient-to-br from-green-50 via-white to-green-100': reportType === 'found',
                         'bg-white': reportType !== 'found'
                     }">
                    <div class="p-6">
                        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-green-100 to-green-50 group-hover:from-green-200 group-hover:to-green-100 transition-all duration-300">
                            <svg class="w-8 h-8 transition-colors duration-300"
                                 :class="{
                                     'text-green-600': reportType === 'found' || hoveredType === 'found',
                                     'text-green-400': reportType !== 'found' && hoveredType !== 'found'
                                 }"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-semibold transition-colors duration-300"
                                :class="{
                                    'text-green-700': reportType === 'found' || hoveredType === 'found',
                                    'text-gray-900': reportType !== 'found' && hoveredType !== 'found'
                                }">
                                I Found an Item
                            </h3>
                            <p class="mt-2 text-sm text-gray-500">Report an item you've found and earn rewards</p>
                        </div>
                    </div>
                    <div class="absolute top-3 right-3 transform transition-all duration-300"
                         :class="{
                             'opacity-100 scale-100': reportType === 'found',
                             'opacity-0 scale-0': reportType !== 'found'
                         }">
                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </label>
        </div>
    </div>

    @error('reportType')
        <p class="mt-2 text-sm text-red-600 text-center">{{ $message }}</p>
    @enderror

    <!-- Reward Info for Found Items -->
    <div x-show="reportType === 'found'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-4"
         class="mt-6">
        <div class="bg-gradient-to-r from-blue-50 via-blue-50 to-indigo-50 border border-blue-100 rounded-xl p-6 shadow-sm">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="h-6 w-6 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6.5 2a.5.5 0 00-.5.5v1h4v-1a.5.5 0 00-.5-.5h-3zM4 3V2a2 2 0 012-2h3a2 2 0 012 2v1h2a2 2 0 012 2v2.5a.5.5 0 01-1 0V5a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h3.5a.5.5 0 010 1H4a2 2 0 01-2-2V5a2 2 0 012-2h2z"/>
                            <path d="M2 9h9v1H2V9zm0 3h9v1H2v-1zm12.5-3a.5.5 0 00-.5.5v1h4v-1a.5.5 0 00-.5-.5h-3zm-2 3V8a2 2 0 012-2h3a2 2 0 012 2v1h2a2 2 0 012 2v2.5a.5.5 0 01-1 0V11a1 1 0 00-1-1H12a1 1 0 00-1 1v10a1 1 0 001 1h3.5a.5.5 0 010 1H12a2 2 0 01-2-2V11a2 2 0 012-2h2z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h4 class="text-lg font-semibold text-blue-900 mb-2">Earn Reward Points!</h4>
                    <p class="text-sm text-blue-700 leading-relaxed">
                        You'll receive reward points for helping return lost items to their owners. These points can be converted to real value or used for future services.
                    </p>
                    <div class="mt-4 flex items-center space-x-4">
                        <a href="#" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200">
                            Learn more about rewards
                            <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        <span class="text-blue-300">|</span>
                        <a href="#" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200">
                            View reward history
                            <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
