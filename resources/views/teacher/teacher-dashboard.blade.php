<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Teacher Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6" style="background: linear-gradient(to right, #f59e0b, #d97706); color: white;">
                    <h3 class="text-2xl font-bold">{{ __("Welcome, Professor " . auth()->user()->name . "!") }}</h3>
                    <p class="mt-2">{{ __("Manage your classes and student progress") }}</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div
                            class="bg-blue-50 dark:bg-blue-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-blue-500 rounded-full">
                                    <i class="bi bi-collection text-2xl text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">My Classes</h5>
                                    <p class="text-gray-600 dark:text-gray-400">Manage your assigned classes</p>
                                    <a href="{{ route('class.show') }}"
                                        class="mt-2 inline-block text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">View
                                        Classes →</a>
                                </div>
                            </div>
                        </div>

                        
                        <div
                            class="bg-green-50 dark:bg-green-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-green-500 rounded-full">
                                    <i class="bi bi-clipboard-check text-2xl text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Mark Attendance
                                    </h5>
                                    <p class="text-gray-600 dark:text-gray-400">Record student attendance</p>
                                    <a href="{{ route('activity-presences.index') }}"
                                        class="mt-2 inline-block text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300">Mark
                                        Attendance →</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('partials.schedule-table')
        </div>
    </div>
</x-app-layout>