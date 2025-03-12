<div
    x-data="{
        show: @entangle('show'),
        timer: null,
        startTimer() {
            this.timer = setTimeout(() => {
                this.show = false;
            }, 3000);
        }
    }"
    x-init="$watch('show', value => {
        if (value) {
            if (timer) clearTimeout(timer);
            startTimer();
        }
    })"
    x-show="show"
    x-transition:enter="transform ease-out duration-300 transition"
    x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed bottom-4 right-4 z-50 flex items-center p-4 mb-4 w-full max-w-xs rounded-lg shadow"
    :class="{
        'text-green-500 bg-green-100 dark:bg-green-800 dark:text-green-200': @js($type) === 'success',
        'text-red-500 bg-red-100 dark:bg-red-800 dark:text-red-200': @js($type) === 'error',
        'text-blue-500 bg-blue-100 dark:bg-blue-800 dark:text-blue-200': @js($type) === 'info'
    }"
    role="alert"
>
    <div class="inline-flex flex-shrink-0 justify-center items-center w-8 h-8 rounded-lg"
        :class="{
            'bg-green-200 text-green-500 dark:bg-green-800 dark:text-green-200': @js($type) === 'success',
            'bg-red-200 text-red-500 dark:bg-red-800 dark:text-red-200': @js($type) === 'error',
            'bg-blue-200 text-blue-500 dark:bg-blue-800 dark:text-blue-200': @js($type) === 'info'
        }">
        <template x-if="@js($type) === 'success'">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
        </template>
        <template x-if="@js($type) === 'error'">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </template>
        <template x-if="@js($type) === 'info'">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
        </template>
    </div>
    <div class="ml-3 text-sm font-normal">{{ $message }}</div>
    <button
        type="button"
        class="ml-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 inline-flex h-8 w-8 hover:bg-gray-100 hover:text-gray-900"
        :class="{
            'hover:bg-green-200 dark:hover:bg-green-700 dark:hover:text-green-200': @js($type) === 'success',
            'hover:bg-red-200 dark:hover:bg-red-700 dark:hover:text-red-200': @js($type) === 'error',
            'hover:bg-blue-200 dark:hover:bg-blue-700 dark:hover:text-blue-200': @js($type) === 'info'
        }"
        @click="show = false"
    >
        <span class="sr-only">Close</span>
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
        </svg>
    </button>
</div>
