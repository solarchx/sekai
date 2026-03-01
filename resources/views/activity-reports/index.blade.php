<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Activity Report Management') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6" style="background: linear-gradient(to right, #10b981, #059669); color: white;">
                    <h3 class="text-2xl font-bold">Activity Report Management</h3>
                    <p class="mt-2">View anonymous reports about teacher performance.</p>
                </div>
                <div class="p-6">
                    <x-soft-delete-filter />

                    <form method="GET" action="{{ route('activity-reports.index') }}" class="mb-6">
                        <div class="flex items-end gap-4">
                            <div class="flex-1">
                                <label for="teacher_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Filter by Teacher
                                </label>
                                <select name="teacher_id" id="teacher_id"
                                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                    onchange="this.form.submit()">
                                    <option value="">-- Select a teacher --</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ $teacherId == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if($teacherId)
                                <a href="{{ route('activity-reports.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">
                                    Clear Filter
                                </a>
                            @endif
                        </div>
                    </form>

                    @if($teacherId)
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Reports List ({{ $reports->total() }})</h4>
                        </div>

                        <div class="space-y-4">
                            @forelse($reports as $report)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 {{ $report->deleted_at ? 'opacity-50' : '' }}">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h5 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $report->presence->form->activity->subject->name }}
                                                ({{ $report->presence->form->activity->class->name }})
                                            </h5>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                Teacher: {{ $report->presence->form->activity->teacher->name }}
                                            </p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                Date: {{ $report->presence->form->activity_date->format('M d, Y') }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full">
                                                Score: {{ $report->score }}/3
                                            </span>
                                            @if($report->deleted_at)
                                                <form action="{{ route('activity-reports.restore', $report) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-sm">
                                                        Restore
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('activity-reports.destroy', $report) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-sm">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="border-t border-gray-200 dark:border-gray-600 pt-4 mt-2">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Topic:</p>
                                        <p class="text-gray-800 dark:text-gray-200 mb-2">{{ $report->topic }}</p>
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Details:</p>
                                        <p class="text-gray-800 dark:text-gray-200">{{ $report->details }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 text-center text-gray-500 dark:text-gray-400">
                                    No reports found for this teacher.
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-6">
                            {{ $reports->links() }}
                        </div>
                    @else
                        <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 text-blue-700 dark:text-blue-100">
                            Please select a teacher from the dropdown above to view reports.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>