<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Class Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl" style="background: linear-gradient(to right, #6366f1, #3b82f6); color: white; padding: 20px; border-radius: 8px;">
                    @if ($errorMessage)
                        @if (Auth::user()->role != 'STUDENT')
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">You are not the homeroom teacher of any class.</h3>
                        @else
                            <p class="text-red-500">{{ $errorMessage }}</p>
                        @endif
                    @else
                        <h3 class="text-lg font-medium">{{ $class->name }}</h3>
                        <p class="mt-1 text-sm">Major: {{ $class->major->name }}</p>
                        <p class="mt-1 text-sm">Grade: {{ $class->grade->id }}</p>
                    @endif
                     @if (!$errorMessage)
                        <h3 class="text-sm mt-1">Homeroom Teacher: {{ $homeroomTeacher->name }}</h3>
                    @endif
                </div>
                <div class="max-w-xl">
                    @if (!$errorMessage && Auth::user()->role == 'STUDENT')
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mt-6">Classmates:</h3>
                        <ul class="list-disc list-inside mt-1 text-sm text-gray-600 dark:text-gray-400">
                            @foreach ($students as $student)
                                <li>{{ $student->name }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Lessons Taught</h3>
                    @if ($lessonTaught->isEmpty())
                        <p class="text-gray-600 dark:text-gray-400">No lessons taught yet.</p>
                    @else
                        <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400">
                            @foreach ($lessonTaught as $activity)
                                <li>{{ $activity->name }} ({{ $activity->subject->name }})</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
        </div>
    </div>
</x-app-layout>
