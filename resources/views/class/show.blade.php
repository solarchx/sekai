<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Class Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
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
                </div>
                <div class="max-w-xl">
                    @if (!$errorMessage)
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Homeroom Teacher: {{ $class->teacher }}</h3>
                    @endif
                    @if (!$errorMessage && Auth::user()->role == 'STUDENT')
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Classmates:</h3>
                        <ul class="list-disc list-inside mt-1 text-sm text-gray-600 dark:text-gray-400">
                            @foreach ($class->students as $student)
                                <li>{{ $student->name }}</li>
                            @endforeach
                        </ul>
                    @endif
                    @if (Auth::user()->role != 'STUDENT')
                        <p class="text-md font-medium text-gray-900 dark:text-gray-100">Lesson Taught</p>
                        @php
                            $weekdays = [
                                0 => 'Monday',
                                1 => 'Tuesday',
                                2 => 'Wednesday',
                                3 => 'Thursday',
                                4 => 'Friday',
                                5 => 'Saturday',
                                6 => 'Sunday',
                            ];
                        @endphp
                        @foreach ($lessonTaught as $lesson)
                            @php
                                $period = $lesson->period;
                                $weekdayName = $weekdays[$period->weekday] ?? 'Unknown';
                                $timeBegin = $period->time_begin->format('H:i');
                                $timeEnd   = $period->time_end->format('H:i');
                                $lessonPeriod = "{$weekdayName}, {$timeBegin} - {$timeEnd}";
                            @endphp
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $lesson->class->name }}</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $lessonPeriod }}</p>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $lesson->subject->name }}</p>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
