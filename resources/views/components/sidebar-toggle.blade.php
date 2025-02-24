<!-- resources/views/components/sidebar-toggle.blade.php -->
<button @click="open = !open"
    class="fixed top-4 left-4 z-50 p-2 bg-gray-800 bg-opacity-50 text-white text-opacity-75 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
    <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
        stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
</button>
<button @click="open = false" x-show="open"
    class="fixed top-4 left-4 z-50 p-2 bg-gray-800 bg-opacity-50 text-white text-opacity-75 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
    </svg>
</button>
