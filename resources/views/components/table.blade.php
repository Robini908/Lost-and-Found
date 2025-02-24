<!-- filepath: /c:/my-projects/lost-found/resources/views/components/table.blade.php -->
<div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            {{ $head }}
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            {{ $body }}
        </tbody>
    </table>
</div>