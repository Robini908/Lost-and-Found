<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
    <h1 class="text-2xl font-bold mb-6">Manage Categories</h1>

    <!-- Add Category Form -->
    <div class="mb-6">
        <form wire:submit.prevent="addCategory">
            <div class="flex gap-4">
                <x-input type="text" wire:model="name" placeholder="Enter category name" class="w-full" />
                <x-button type="submit">Add Category</x-button>
            </div>
            <x-input-error for="name" class="mt-2" />
        </form>
    </div>

    <!-- Category List -->
    <div class="bg-white shadow-lg rounded-lg p-6">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="text-left">Name</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>
                            @if ($editCategoryId === $category->id)
                                <input type="text" wire:model="editName" class="w-full" />
                                <x-input-error for="editName" class="mt-2" />
                            @else
                                {{ $category->name }}
                            @endif
                        </td>
                        <td class="text-right">
                            @if ($editCategoryId === $category->id)
                                <x-button wire:click="updateCategory">Save</x-button>
                                <x-button wire:click="resetEdit">Cancel</x-button>
                            @else
                                <x-button wire:click="editCategory({{ $category->id }})">Edit</x-button>
                                <x-button wire:click="deleteCategory({{ $category->id }})">Delete</x-button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $categories->links() }}
    </div>
</div>
