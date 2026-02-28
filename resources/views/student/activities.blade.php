<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Activity Forms') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6" style="background: linear-gradient(to right, #6366f1, #3b82f6); color: white;">
                    <h3 class="text-2xl font-bold">Activity Forms</h3>
                    <p class="mt-2">Select an activity to view its forms and submit your attendance.</p>
                </div>
                <div class="p-6">
                    {{-- Activity selector --}}
                    <form method="GET" action="{{ route('student.activities') }}" id="activity-form">
                        <div class="mb-6 flex items-end gap-4">
                            <div class="flex-1">
                                <label for="activity_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Choose an activity
                                </label>
                                <select name="activity_id" id="activity_id" 
                                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                    onchange="this.form.submit()">
                                    <option value="">-- Select an activity --</option>
                                    @foreach($activities as $activity)
                                        <option value="{{ $activity->id }}" {{ $selectedActivity && $selectedActivity->id == $activity->id ? 'selected' : '' }}>
                                            {{ $activity->subject->name }} – {{ $activity->class->name }} ({{ $activity->period->weekday_name }} {{ $activity->period->time_begin }}-{{ $activity->period->time_end }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <noscript>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Go</button>
                            </noscript>
                        </div>
                    </form>

                    @if($selectedActivity)
                        <div class="mt-8">
                            <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                Forms for {{ $selectedActivity->subject->name }}
                            </h4>

                            @if($forms->isEmpty())
                                <p class="text-gray-500 dark:text-gray-400">No forms have been created for this activity yet.</p>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-full bg-white dark:bg-gray-800">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @php
                                                $now = \Carbon\Carbon::now();
                                            @endphp
                                            @foreach($forms as $form)
                                                @php
                                                    $presence = $form->presences->first();
                                                    $report = $presence ? $presence->report : null;

                                                    $formDate = $form->activity_date->format('Y-m-d');
                                                    $startDateTime = \Carbon\Carbon::parse($formDate . ' ' . $selectedActivity->period->time_begin);
                                                    $endDateTime = \Carbon\Carbon::parse($formDate . ' ' . $selectedActivity->period->time_end);
                                                    $windowStart = $startDateTime->copy()->subMinutes(15);
                                                    $windowEnd = $endDateTime->copy()->addMinutes(15);
                                                    $now = \Carbon\Carbon::now();
                                                    $canSubmit = $now->between($windowStart, $windowEnd);
                                                @endphp
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                        {{ $form->activity_date->format('M d, Y') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                        @if($presence)
                                                            @php
                                                                $statusLabels = [0 => 'Absent', 1 => 'Permitted Leave', 2 => 'Sick Leave', 3 => 'Present'];
                                                                $statusColors = [0 => 'red', 1 => 'yellow', 2 => 'orange', 3 => 'green'];
                                                            @endphp
                                                            <span class="bg-{{ $statusColors[$presence->score] }}-100 dark:bg-{{ $statusColors[$presence->score] }}-900 text-{{ $statusColors[$presence->score] }}-800 dark:text-{{ $statusColors[$presence->score] }}-100 px-3 py-1 rounded-full text-xs font-semibold">
                                                                {{ $statusLabels[$presence->score] }}
                                                            </span>
                                                        @else
                                                            <span class="text-gray-400">Not submitted</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                        @if($presence)
                                                            @if($report)
                                                                <a href="{{ route('activity-reports.edit', $report) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-xs">Edit Report</a>
                                                                <form action="{{ route('activity-reports.destroy', $report) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-xs">Delete Report</button>
                                                                </form>
                                                            @else
                                                                <span class="text-gray-500">No report yet</span>
                                                            @endif
                                                        @else
                                                            @if($canSubmit)
                                                                <a href="{{ route('activity-presences.create', ['form_id' => $form->id, 'student_id' => auth()->id()]) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-xs">Submit Presence</a>
                                                            @else
                                                                <span class="text-gray-400">Submission closed</span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @elseif($activities->isNotEmpty())
                        <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 text-blue-700 dark:text-blue-100 mt-6">
                            Please select an activity from the dropdown above.
                        </div>
                    @else
                        <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 text-yellow-700 dark:text-yellow-100 mt-6">
                            You are not enrolled in any activities.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>