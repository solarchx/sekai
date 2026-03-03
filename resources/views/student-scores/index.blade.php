<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Student Scores for') }} {{ $activity->subject->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6" style="background: linear-gradient(to right, #ef4444, #dc2626); color: white;">
                    <h3 class="text-2xl font-bold">{{ $activity->subject->name }}</h3>
                    <p class="mt-2">{{ __('Class:') }} {{ $activity->class->name }} – {{ __('Teacher:') }}
                        {{ $activity->teacher->name }}</p>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-4 py-2 border text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Student</th>
                                    @foreach($distributions as $dist)
                                        <th
                                            class="px-4 py-2 border text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            {{ $dist->name }}<br><span class="text-xs">({{ $dist->weight }}%)</span>
                                        </th>
                                    @endforeach
                                    <th
                                        class="px-4 py-2 border text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($students as $student)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-2 border text-sm text-gray-900 dark:text-gray-100">
                                            {{ $student->name }}
                                        </td>
                                        @foreach($distributions as $dist)
                                            @php
                                                $score = $scores[$student->id][$dist->id] ?? null;
                                                $scoreValue = $score ? $score->score : 0;
                                            @endphp
                                            <td class="px-4 py-2 border text-sm text-gray-900 dark:text-gray-100">
                                                {{ $scoreValue }}
                                            </td>
                                        @endforeach
                                        <td class="px-4 py-2 border text-sm font-medium">
                                            <a href="{{ route('student-scores.edit', [$activity, $student]) }}"
                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-xs inline-block">
                                                Edit Scores
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($distributions) + 2 }}"
                                            class="px-4 py-2 border text-center text-gray-500 dark:text-gray-400">
                                            {{ __('No students enrolled in this activity.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('class.show') }}"
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">
                            Back to Activity
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>