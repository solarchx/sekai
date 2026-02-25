<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Activity Details') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Activity Information Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6" style="background: linear-gradient(to right, #06b6d4, #0891b2); color: white;">
                    <h3 class="text-2xl font-bold">{{ $activity->subject->name ?? 'N/A' }}</h3>
                    <p class="mt-2">{{ $activity->class->name ?? 'N/A' }} - Taught by {{ $activity->teacher->name ?? 'N/A' }}</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subject</label>
                            <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ $activity->subject->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teacher</label>
                            <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ $activity->teacher->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Class</label>
                            <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ $activity->class->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Period</label>
                            <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">
                                {{ $activity->period->weekday_name ?? 'N/A' }} {{ $activity->period->time_begin }} - {{ $activity->period->time_end }}
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons for Teachers+ -->
                    @if(auth()->user()->role !== 'STUDENT')
                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="{{ route('activities.edit', $activity) }}" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Activity
                            </a>
                            <a href="{{ route('score-distributions.index', ['activity_id' => $activity->id]) }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Score Distribution
                            </a>
                            <a href="{{ route('student-scores.index', ['activity_id' => $activity->id]) }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                Student Scores
                            </a>
                            <form action="{{ route('activities.destroy', $activity) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors inline-flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete Activity
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity Forms Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6" style="background: linear-gradient(to right, #06b6d4, #0891b2); color: white;">
                    <h3 class="text-2xl font-bold">Activity Forms</h3>
                    <p class="mt-2">View and manage attendance forms for this activity.</p>
                </div>
                <div class="p-6">
                    @if(auth()->user()->role !== 'STUDENT')
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Forms List</h4>
                            <a href="{{ route('activity-forms.create', ['activity_id' => $activity->id]) }}" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Form
                            </a>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-800">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Activity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($activity->forms as $form)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $form->activity_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $form->activity->subject->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('activity-forms.show', $form) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-xs transition-colors inline-block">
                                            View Details
                                        </a>
                                        @if(auth()->user()->role !== 'STUDENT')
                                            <a href="{{ route('activity-forms.edit', $form) }}" class="bg-cyan-600 hover:bg-cyan-700 text-white px-3 py-1 rounded-lg text-xs transition-colors inline-block">Edit</a>
                                            <form action="{{ route('activity-forms.destroy', $form) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-xs transition-colors">Delete</button>
                                            </form>
                                        @elseif(!$form->presences()->where('student_id', auth()->id())->exists())
                                            @php
                                                $period = $form->activity->period;
                                                $start = \Carbon\Carbon::parse($period->time_begin);
                                                $end = \Carbon\Carbon::parse($period->time_end);
                                                $windowStart = $start->copy()->subMinutes(15);
                                                $windowEnd = $end->copy()->addMinutes(15);
                                            @endphp
                                            @if($now->between($windowStart, $windowEnd) && $now->format('Y-m-d') == $form->activity_date->format('Y-m-d'))
                                                <a href="{{ route('activity-presences.create', ['form_id' => $form->id]) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-xs transition-colors inline-block">
                                                    Submit Presence
                                                </a>
                                            @endif
                                        @elseif(auth()->user()->role === 'STUDENT')
                                            @php
                                                $presence = $form->presences->firstWhere('student_id', auth()->id());
                                                $report = $presence ? $presence->report : null;
                                            @endphp

                                            @if(!$presence)
                                                @php
                                                    $now = \Carbon\Carbon::now();
                                                    $period = $form->activity->period;
                                                    $start = \Carbon\Carbon::parse($period->time_begin);
                                                    $end = \Carbon\Carbon::parse($period->time_end);
                                                    $windowStart = $start->copy()->subMinutes(15);
                                                    $windowEnd = $end->copy()->addMinutes(15);
                                                @endphp
                                                @if($now->between($windowStart, $windowEnd) && $now->toDateString() == $form->activity_date->toDateString())
                                                    <a href="{{ route('activity-presences.create', ['form_id' => $form->id]) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-xs transition-colors inline-block">
                                                        Submit Presence
                                                    </a>
                                                @endif
                                            @elseif($report)
                                                <a href="{{ route('activity-reports.edit', $report) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-xs transition-colors inline-block">Edit Report</a>
                                                <form action="{{ route('activity-reports.destroy', $report) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-xs transition-colors">Delete Report</button>
                                                </form>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No forms found for this activity.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
