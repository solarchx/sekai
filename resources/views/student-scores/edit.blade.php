<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Scores for ') }} {{ $student->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                        {{ $activity->subject->name }} – {{ $student->name }}
                    </h3>
                    <p class="mb-4 text-gray-600 dark:text-gray-400">
                        {{ __('Class:') }} {{ $activity->class->name }}
                    </p>

                    <form method="POST" action="{{ route('student-scores.update', [$activity, $student]) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <div class="overflow-x-auto">
                                <table
                                    class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th
                                                class="px-4 py-2 border text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('Component (Weight)') }}</th>
                                            <th
                                                class="px-4 py-2 border text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('Score (0-100)') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($distributions as $dist)
                                            @php
                                                $existingScore = $existingScores[$dist->id] ?? null;
                                                $scoreValue = old('scores.' . $dist->id, $existingScore ? $existingScore->score : 0);
                                            @endphp
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td class="px-4 py-2 border text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $dist->name }} ({{ $dist->weight }}%)
                                                </td>
                                                <td class="px-4 py-2 border">
                                                    <input type="number" name="scores[{{ $dist->id }}]"
                                                        value="{{ $scoreValue }}" min="0" max="100"
                                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('scores.' . $dist->id) is-invalid @enderror"
                                                        required>
                                                    @error('scores.' . $dist->id)
                                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                                    @enderror
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('student-scores.index', $activity) }}"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">{{ __('Cancel') }}</a>
                            <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">{{ __('Update Scores') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>