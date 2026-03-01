<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="pt-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6" style="background: linear-gradient(to right, #3b82f6, #8b5cf6); color: white;">
                    <h3 class="text-2xl font-bold">{{ __("Welcome back, " . auth()->user()->name . "!") }}</h3>
                    <p class="mt-2">{{ __("You're logged in as Administrator. Manage the entire system.") }}</p>
                </div>
                <div class="p-6">
                    <h4 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Admin Panel</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-blue-50 dark:bg-blue-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-blue-500 rounded-full">
                                    <i class="bi bi-people text-2xl text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Users</h5>
                                    <p class="text-gray-600 dark:text-gray-400">Manage user accounts</p>
                                    <a href="{{ route('users.index') }}" class="mt-2 inline-block text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">View Users</a>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-green-500 rounded-full">
                                    <i class="bi bi-file-earmark text-2xl text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Majors</h5>
                                    <p class="text-gray-600 dark:text-gray-400">Handle academic majors</p>
                                    <a href="{{ route('majors.index') }}" class="mt-2 inline-block text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300">View Majors</a>
                                </div>
                            </div>
                        </div>
                        <div class="bg-purple-50 dark:bg-purple-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-purple-500 rounded-full">
                                    <i class="bi bi-building text-2xl text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Classes</h5>
                                    <p class="text-gray-600 dark:text-gray-400">Manage class groups</p>
                                    <a href="{{ route('classes.index') }}" class="mt-2 inline-block text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300">View Classes</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="pt-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mt-6">
                <div class="p-6" style="background: linear-gradient(to right, #3b82f6, #8b5cf6); color: white;">
                    <h3 class="text-2xl font-bold">System Overview</h3>
                    <p class="mt-2">Quick stats about the system.</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-blue-50 dark:bg-blue-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Total Users</h5>
                            <p class="text-gray-600 dark:text-gray-400 text-2xl">{{ $users }}</p>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Total Classes</h5>
                            <p class="text-gray-600 dark:text-gray-400 text-2xl">{{ $classes }}</p>
                        </div>
                        <div class="bg-purple-50 dark:bg-purple-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Total Activities</h5>
                            <p class="text-gray-600 dark:text-gray-400 text-2xl">{{ $activities }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @include('partials.schedule-table')
        </div>
    </div>
</x-app-layout>
