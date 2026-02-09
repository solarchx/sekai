<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6" style="background: linear-gradient(to right, #3b82f6, #8b5cf6); color: white;">
                    <h3 class="text-2xl font-bold">{{ __("Welcome back, " . auth()->user()->name . "!") }}</h3>
                    <p class="mt-2">{{ __("You're logged in as " . strtolower(auth()->user()->role) . ".") }}</p>
                </div>
                <div class="p-6">
                    @if(auth()->user()->role === 'ADMIN')
                        <h4 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Admin Panel</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-blue-50 dark:bg-blue-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                                <div class="flex items-center">
                                    <div class="p-3 bg-blue-500 rounded-full">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Users</h5>
                                        <p class="text-gray-600 dark:text-gray-400">Manage user accounts</p>
                                        <a href="{{ route('users.index') }}" class="mt-2 inline-block text-blue-600 hover:text-blue-800">View Users</a>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                                <div class="flex items-center">
                                    <div class="p-3 bg-green-500 rounded-full">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Majors</h5>
                                        <p class="text-gray-600 dark:text-gray-400">Handle academic majors</p>
                                        <a href="{{ route('majors.index') }}" class="mt-2 inline-block text-green-600 hover:text-green-800">View Majors</a>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-purple-50 dark:bg-purple-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                                <div class="flex items-center">
                                    <div class="p-3 bg-purple-500 rounded-full">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Classes</h5>
                                        <p class="text-gray-600 dark:text-gray-400">Manage class groups</p>
                                        <a href="{{ route('classes.index') }}" class="mt-2 inline-block text-purple-600 hover:text-purple-800">View Classes</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-600 dark:text-gray-400">{{ __("Enjoy your session!") }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
