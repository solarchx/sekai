<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Activity Form Details') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Activity Form Information Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6" style="background: linear-gradient(to right, #f59e0b, #f97316); color: white;">
                    <h3 class="text-2xl font-bold">{{ $form->activity->subject->name ?? 'N/A' }} - {{ \Carbon\Carbon::parse($form->activity_date)->format('M d, Y') }}</h3>
                    <p class="mt-2">{{ $form->activity->class->name ?? 'N/A' }} - Taught by {{ $form->activity->teacher->name ?? 'N/A' }}</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                            <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($form->activity_date)->format('l, M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Activity</label>
                            <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ $form->activity->subject->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Period</label>
                            <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">
                                {{ $form->activity->period->time_begin }} - {{ $form->activity->period->time_end }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Class</label>
                            <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ $form->activity->class->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Action Buttons for Teachers+ -->
                    @if(auth()->user()->role !== 'STUDENT')
                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="{{ route('activity-forms.edit', $form) }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Form
                            </a>
                            <form action="{{ route('activity-forms.destroy', $form) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors inline-flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete Form
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity Presences Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6" style="background: linear-gradient(to right, #f59e0b, #f97316); color: white;">
                    <h3 class="text-2xl font-bold">Attendance Records</h3>
                    <p class="mt-2">View and manage student attendance for this form.</p>
                </div>
                <div class="p-6">
                    @if(auth()->user()->role !== 'STUDENT')
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Students List</h4>
                            <a href="{{ route('activity-presences.create', ['form_id' => $form->id]) }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Record Presence
                            </a>
                        </div>
                    @endif

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
                                        $presence = $form->presences()->where('student_id', $student->id)->first();
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
                                            @if(auth()->user()->role !== 'STUDENT')
                                                @if($presence)
                                                    <a href="{{ route('activity-presences.edit', $presence) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-xs transition-colors inline-block">Edit</a>
                                                    <form action="{{ route('activity-presences.destroy', $presence) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-xs transition-colors">Delete</button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('activity-presences.create', ['form_id' => $form->id, 'student_id' => $student->id]) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-xs transition-colors inline-block">Record</a>
                                                @endif
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
            </div>
        </div>
    </div>
</x-app-layout>