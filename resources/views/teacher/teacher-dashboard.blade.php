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
                        <!-- My Classes -->
                        <div class="bg-blue-50 dark:bg-blue-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-blue-500 rounded-full">
                                    <i class="bi bi-collection text-2xl text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">My Classes</h5>
                                    <p class="text-gray-600 dark:text-gray-400">Manage your assigned classes</p>
                                    <a href="{{ route('class.show') }}" class="mt-2 inline-block text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">View Classes →</a>
                                </div>
                            </div>
                        </div>

                        <!-- Mark Attendance -->
                        <div class="bg-green-50 dark:bg-green-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-green-500 rounded-full">
                                    <i class="bi bi-clipboard-check text-2xl text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Mark Attendance</h5>
                                    <p class="text-gray-600 dark:text-gray-400">Record student attendance</p>
                                    <a href="#" class="mt-2 inline-block text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300">Mark Attendance →</a>
                                </div>
                            </div>
                        </div>

                        <!-- Record Grades -->
                        <div class="bg-purple-50 dark:bg-purple-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-purple-500 rounded-full">
                                    <i class="bi bi-pencil-square text-2xl text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Record Grades</h5>
                                    <p class="text-gray-600 dark:text-gray-400">Enter and manage student grades</p>
                                    <a href="#" class="mt-2 inline-block text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300">Record Grades →</a>
                                </div>
                            </div>
                        </div>

                        <!-- Class Materials -->
                        <div class="bg-red-50 dark:bg-red-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-red-500 rounded-full">
                                    <i class="bi bi-pencil-square text-2xl text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Class Materials</h5>
                                    <p class="text-gray-600 dark:text-gray-400">Upload and manage class materials</p>
                                    <a href="#" class="mt-2 inline-block text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">Manage Materials →</a>
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
