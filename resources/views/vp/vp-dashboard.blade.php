<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Vice Principal Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6" style="background: linear-gradient(to right, #8b5cf6, #a78bfa); color: white;">
                    <h3 class="text-2xl font-bold">{{ __("Welcome, VP " . auth()->user()->name . "!") }}</h3>
                    <p class="mt-2">{{ __("Oversee academic operations and review reports") }}</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div class="bg-blue-50 dark:bg-blue-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-blue-500 rounded-full">
                                    <i class="bi bi-graph-up text-2xl text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Academic Reports</h5>
                                    <p class="text-gray-600 dark:text-gray-400">View performance analytics</p>
                                    <a href="#" class="mt-2 inline-block text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">View Reports →</a>
                                </div>
                            </div>
                        </div>

                        
                        <div class="bg-green-50 dark:bg-green-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-green-500 rounded-full">
                                    <i class="bi bi-buildings text-2xl text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Class Overview</h5>
                                    <p class="text-gray-600 dark:text-gray-400">Monitor all classes</p>
                                    <a href="#" class="mt-2 inline-block text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300">View Classes →</a>
                                </div>
                            </div>
                        </div>

                        
                        <div class="bg-purple-50 dark:bg-purple-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-purple-500 rounded-full">
                                    <i class="bi bi-person-badge text-2xl text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Teacher Performance</h5>
                                    <p class="text-gray-600 dark:text-gray-400">Review teacher evaluations</p>
                                    <a href="#" class="mt-2 inline-block text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300">View Evaluations →</a>
                                </div>
                            </div>
                        </div>

                        
                        <div class="bg-yellow-50 dark:bg-yellow-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-yellow-500 rounded-full">
                                    <i class="bi bi-bar-chart text-2xl text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Attendance Summary</h5>
                                    <p class="text-gray-600 dark:text-gray-400">School-wide attendance statistics</p>
                                    <a href="{{ route('activity-presences.index') }}" class="mt-2 inline-block text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300">View Summary →</a>
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
