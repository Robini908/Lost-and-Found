<div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{ activeTab: 'roles' }">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 leading-tight">
                    System Management
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Manage roles, permissions, and user access control
                </p>
            </div>
            <div class="flex space-x-3">
                <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-sm font-medium">
                    {{ $totalUsers }} Users
                </span>
                <span class="bg-green-50 text-green-600 px-3 py-1 rounded-full text-sm font-medium">
                    {{ $totalRoles }} Roles
                </span>
                <span class="bg-purple-50 text-purple-600 px-3 py-1 rounded-full text-sm font-medium">
                    {{ $totalPermissions }} Permissions
                </span>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="mb-8 bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="flex items-center p-2">
            <!-- Roles Tab -->
            <button
                @click="activeTab = 'roles'"
                :class="{ 'bg-blue-50 text-blue-700 border-blue-700': activeTab === 'roles' }"
                class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 border-b-2 border-transparent hover:text-blue-600 flex-1"
            >
                <i class="fas fa-user-shield mr-2"></i>
                Role Management
            </button>

            <!-- Permissions Tab -->
            <button
                @click="activeTab = 'permissions'"
                :class="{ 'bg-green-50 text-green-700 border-green-700': activeTab === 'permissions' }"
                class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 border-b-2 border-transparent hover:text-green-600 flex-1"
            >
                <i class="fas fa-key mr-2"></i>
                Permissions
            </button>

            <!-- User Roles Tab -->
            <button
                @click="activeTab = 'user-roles'"
                :class="{ 'bg-purple-50 text-purple-700 border-purple-700': activeTab === 'user-roles' }"
                class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 border-b-2 border-transparent hover:text-purple-600 flex-1"
            >
                <i class="fas fa-users-cog mr-2"></i>
                User Role Assignment
            </button>

            <!-- Impersonation Tab -->
            <button
                @click="activeTab = 'impersonation'"
                :class="{ 'bg-amber-50 text-amber-700 border-amber-700': activeTab === 'impersonation' }"
                class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 border-b-2 border-transparent hover:text-amber-600 flex-1"
            >
                <i class="fas fa-user-secret mr-2"></i>
                User Impersonation
            </button>
        </div>
    </div>

    <!-- Content Area -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Roles Management Content -->
        <div x-show="activeTab === 'roles'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Role Management</h3>
                    <p class="text-sm text-gray-500 mt-1">Configure user roles and their permissions</p>
                </div>
                <div class="p-2 bg-blue-50 rounded-lg">
                    <i class="fas fa-user-shield text-blue-500 text-xl"></i>
                </div>
            </div>
            <livewire:manage-user-types />
        </div>

        <!-- Permissions Management Content -->
        <div x-show="activeTab === 'permissions'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Permission Management</h3>
                    <p class="text-sm text-gray-500 mt-1">Manage system permissions</p>
                </div>
                <div class="p-2 bg-green-50 rounded-lg">
                    <i class="fas fa-key text-green-500 text-xl"></i>
                </div>
            </div>
            <livewire:manage-permissions />
        </div>

        <!-- User Role Assignment Content -->
        <div x-show="activeTab === 'user-roles'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">User Role Assignment</h3>
                    <p class="text-sm text-gray-500 mt-1">Assign roles to system users</p>
                </div>
                <div class="p-2 bg-purple-50 rounded-lg">
                    <i class="fas fa-users-cog text-purple-500 text-xl"></i>
                </div>
            </div>
            <livewire:manage-user-roles />
        </div>

        <!-- User Impersonation Content -->
        <div x-show="activeTab === 'impersonation'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">User Impersonation</h3>
                    <p class="text-sm text-gray-500 mt-1">Switch between user accounts</p>
                </div>
                <div class="p-2 bg-amber-50 rounded-lg">
                    <i class="fas fa-user-secret text-amber-500 text-xl"></i>
                </div>
            </div>
            <livewire:impersonate-user />
        </div>
    </div>

    <!-- Success Notification -->
    <div
        x-data="{ show: false, message: '' }"
        x-on:settings-saved.window="show = true; message = $event.detail; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition
        class="fixed bottom-4 right-4 z-50"
    >
        <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
            <i class="fas fa-check-circle"></i>
            <p x-text="message"></p>
        </div>
    </div>
</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
