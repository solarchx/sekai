<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Lesson Period Schedule Sheet') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6" style="background: linear-gradient(to right, #7c3aed, #a855f7); color: white;">
                    <h3 class="text-2xl font-bold">Lesson Period Schedule Sheet</h3>
                    <p class="mt-2">View and manage lesson periods by academic time.</p>
                </div>
                <div class="p-6">
                    <!-- Academic Time Selector -->
                    <div class="mb-6 flex items-center gap-4">
                        <label for="semester_id"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Academic
                            Time:</label>
                        <select id="semester_id" name="semester_id"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                            onchange="filterPeriods()">
                            <option value="">-- Choose Academic Time --</option>
                            @foreach($semesters as $semester)
                                <option value="{{ $semester->id }}" {{ $selectedSemesterId == $semester->id ? 'selected' : '' }}>
                                    {{ $semester->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Schedule Sheet Table -->
                    @if($selectedSemesterId && $periods->count() > 0)
                        <x-schedule-sheet :parentPeriods="$parentPeriods" :periods="$periods" :isReadOnly="false" />
                    @elseif($selectedSemesterId)
                        <div
                            class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 text-blue-700 dark:text-blue-100">
                            No periods found for this academic time. Create a new one to get started.
                        </div>
                    @else
                        <div
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4 text-gray-600 dark:text-gray-300">
                            Select an academic time from the dropdown above to view periods.
                        </div>
                    @endif

                    <!-- Create Button -->
                    <div class="mt-6">
                        <a href="{{ route('periods.create') }}"
                            class="bg-violet-600 hover:bg-violet-700 text-white px-6 py-3 rounded-lg shadow-md transition-colors inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add New Period
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function filterPeriods() {
            const semesterId = document.getElementById('semester_id').value;
            if (semesterId) {
                window.location.href = '{{ route("periods.index") }}?semester_id=' + semesterId;
            } else {
                window.location.href = '{{ route("periods.index") }}';
            }
        }
    </script>
</x-app-layout>