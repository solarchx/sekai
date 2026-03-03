<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Grades') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6" style="background: linear-gradient(to right, #a855f7, #ec4899); color: white;">
                    <h3 class="text-2xl font-bold">{{ __('My Grades') }}</h3>
                    <p class="mt-2">{{ __('Select a semester to view your grades.') }}</p>
                </div>
                <div class="p-6">
                    <form method="GET" action="{{ route('student.grades') }}" id="semester-form">
                        <div class="mb-6 flex items-end gap-4">
                            <div class="flex-1">
                                <label for="semester_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Choose a semester') }}
                                </label>
                                <select name="semester_id" id="semester_id"
                                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                    onchange="this.form.submit()">
                                    <option value="">{{ __('-- Select a semester --') }}</option>
                                    @foreach($semesters as $semester)
                                        <option value="{{ $semester->id }}" {{ $selectedSemesterId == $semester->id ? 'selected' : '' }}>
                                            {{ $semester->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <noscript>
                                <button type="submit"
                                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">Go</button>
                            </noscript>
                        </div>
                    </form>

                    @if($selectedSemesterId)
                        @if($activities->isEmpty())
                            <div
                                class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 text-yellow-700 dark:text-yellow-100 mt-6">
                                {{ __('No grades found for this semester.') }}
                            </div>
                        @else
                            <div class="space-y-8">
                                @foreach($activities as $activity)
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $activity->subject->name }}</h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">Teacher:
                                                    {{ $activity->teacher->name }}</p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $activity->period->weekday_name }}
                                                    {{ $activity->period->time_begin }}–{{ $activity->period->time_end }}</p>
                                            </div>
                                            <div class="text-right">
                                                <span
                                                    class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $activity->weighted_total }}</span>
                                                <span class="text-sm text-gray-500 dark:text-gray-400"> / 100</span>
                                            </div>
                                        </div>

                                        <div class="overflow-x-auto">
                                            <table class="min-w-full bg-white dark:bg-gray-800 rounded-lg">
                                                <thead class="bg-gray-100 dark:bg-gray-600">
                                                    <tr>
                                                        <th
                                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                            Score Component</th>
                                                        <th
                                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                            Weight</th>
                                                        <th
                                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                            Your Score</th>
                                                        <th
                                                            class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                            Contribution</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                    @foreach($activity->breakdown as $item)
                                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                                                {{ $item['name'] }}</td>
                                                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                                                {{ $item['weight'] }} ({{ number_format($item['weight_percent'], 1) }}%)
                                                            </td>
                                                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                                                {{ $item['score'] }}</td>
                                                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                                                {{ number_format($item['contribution'], 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot class="bg-gray-50 dark:bg-gray-600">
                                                    <tr>
                                                        <td colspan="3"
                                                            class="px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 text-right">
                                                            Weighted Total</td>
                                                        <td
                                                            class="px-4 py-2 text-sm font-bold text-purple-600 dark:text-purple-400">
                                                            {{ $activity->weighted_total }}</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @elseif($semesters->isNotEmpty())
                        <div
                            class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 text-blue-700 dark:text-blue-100 mt-6">
                            Please select a semester from the dropdown above.
                        </div>
                    @else
                        <div
                            class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 text-yellow-700 dark:text-yellow-100 mt-6">
                            No semesters available.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>