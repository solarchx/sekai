<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Student Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6" style="background: linear-gradient(to right, #06b6d4, #0891b2); color: white;">
                    <h3 class="text-2xl font-bold">{{ __("Welcome, " . auth()->user()->name . "!") }}</h3>
                    <p class="mt-2">{{ __("Track your academic progress and stay updated") }}</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div
                            class="bg-blue-50 dark:bg-blue-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-blue-500 rounded-full">
                                    <i class="bi bi-book text-2xl text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">My Classes</h5>
                                    <p class="text-gray-600 dark:text-gray-400">View enrolled classes</p>
                                    <a href="{{ route('class.show') }}"
                                        class="mt-2 inline-block text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">View
                                        Classes →</a>
                                </div>
                            </div>
                        </div>

                        
                        <div class="bg-indigo-50 dark:bg-indigo-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-indigo-500 rounded-full">
                                    <i class="bi bi-calendar-check text-2xl text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">My Activities</h5>
                                    <p class="text-gray-600 dark:text-gray-400">View and submit attendance for your classes</p>
                                    <a href="{{ route('student.activities') }}" class="mt-2 inline-block text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">Go to Activities →</a>
                                </div>
                            </div>
                        </div>

                        
                        <div class="bg-purple-50 dark:bg-purple-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-purple-500 rounded-full">
                                    <i class="bi bi-bar-chart-line text-2xl text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">My Grades</h5>
                                    <p class="text-gray-600 dark:text-gray-400">View your performance by semester</p>
                                    <a href="{{ route('student.grades') }}" class="mt-2 inline-block text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300">View Grades →</a>
                                </div>
                            </div>
                        </div>

                        
                        <div
                            class="bg-yellow-50 dark:bg-yellow-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-yellow-500 rounded-full">
                                    <i class="bi bi-megaphone text-2xl text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Announcements</h5>
                                    <p class="text-gray-600 dark:text-gray-400">Latest school announcements</p>
                                    <a href=""{{ route('announcements.index') }}""
                                        class="mt-2 inline-block text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300">View
                                        Announcements →</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.schedule-table')
</x-app-layout>