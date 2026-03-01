<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Activity Presence Management') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6" style="background: linear-gradient(to right, #3b82f6, #1d4ed8); color: white;">
                    <h3 class="text-2xl font-bold">Activity Presence Management</h3>
                    <p class="mt-2">Select an activity and a form to view or manage attendance.</p>
                </div>
                <div class="p-6">
                    <form method="GET" action="{{ route('activity-presences.index') }}" id="activity-form">
                        <div class="mb-4 flex items-end gap-4">
                            <div class="flex-1">
                                <label for="activity_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Activity
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
                        </div>

                        @if($selectedActivity)
                            <div class="mb-4 flex items-end gap-4">
                                <div class="flex-1">
                                    <label for="form_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Activity Form
                                    </label>
                                    <select name="form_id" id="form_id" 
                                        class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                        onchange="this.form.submit()">
                                        <option value="">-- Select a form --</option>
                                        @foreach($forms as $form)
                                            <option value="{{ $form->id }}" {{ $selectedForm && $selectedForm->id == $form->id ? 'selected' : '' }}>
                                                {{ $form->activity_date->format('M d, Y') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                    </form>

                    @if($selectedForm)
                        <div class="mt-6">
                            <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                Attendance for {{ $selectedForm->activity_date->format('M d, Y') }}
                            </h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white dark:bg-gray-800">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Student</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Location</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @php
                                            $statusLabels = [0 => 'Absent', 1 => 'Permitted Leave', 2 => 'Sick Leave', 3 => 'Present'];
                                            $statusColors = [0 => 'red', 1 => 'yellow', 2 => 'orange', 3 => 'green'];
                                        @endphp
                                        @forelse($students as $student)
                                            @php
                                                $presence = $presences[$student->id] ?? null;
                                            @endphp
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $student->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    @if($presence)
                                                        <span class="bg-{{ $statusColors[$presence->score] }}-100 dark:bg-{{ $statusColors[$presence->score] }}-900 text-{{ $statusColors[$presence->score] }}-800 dark:text-{{ $statusColors[$presence->score] }}-100 px-3 py-1 rounded-full text-xs font-semibold">
                                                            {{ $statusLabels[$presence->score] }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400">No record</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $presence ? ($presence->location ?? 'N/A') : '--' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                    @if($presence)
                                                        <a href="{{ route('activity-presences.edit', $presence) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-xs inline-block">Edit</a>
                                                    @else
                                                        <a href="{{ route('activity-presences.create', ['form_id' => $selectedForm->id, 'student_id' => $student->id]) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-xs inline-block">Record</a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No students enrolled in this class.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @elseif($selectedActivity)
                        <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 text-blue-700 dark:text-blue-100 mt-6">
                            Please select a form from the dropdown above.
                        </div>
                    @elseif($activities->isEmpty())
                        <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 text-yellow-700 dark:text-yellow-100 mt-6">
                            No activities available.
                        </div>
                    @else
                        <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4 text-gray-600 dark:text-gray-300 mt-6">
                            Select an activity from the dropdown above.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>