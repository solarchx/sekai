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
                        <label for="semester_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Academic Time:</label>
                        <select id="semester_id" name="semester_id" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" onchange="filterPeriods()">
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
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full bg-white dark:bg-gray-800">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Period ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Monday</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tuesday</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Wednesday</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Thursday</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Friday</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Saturday</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Sunday</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($parentPeriods as $parentPeriod)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $parentPeriod->time_begin }} - {{ $parentPeriod->time_end }}
                                            </td>
                                            @php
                                                $childPeriods = $periods->filter(fn($p) => $p->parent_id == $parentPeriod->id)->keyBy('weekday');
                                            @endphp
                                            @for ($day = 0; $day < 7; $day++)
                                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                                    @if(isset($childPeriods[$day]))
                                                        <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-100 px-3 py-1 rounded-full text-xs font-semibold">
                                                            ✓ Scheduled
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400">--</span>
                                                    @endif
                                                </td>
                                            @endfor
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <a href="{{ route('periods.edit', $parentPeriod) }}" class="bg-cyan-600 hover:bg-cyan-700 text-white px-3 py-1 rounded-lg text-xs transition-colors inline-block">Edit</a>
                                                <form action="{{ route('periods.destroy', $parentPeriod) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure? All 7 day periods will be deleted.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-xs transition-colors">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No periods found for this academic time.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @elseif($selectedSemesterId)
                        <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 text-blue-700 dark:text-blue-100">
                            No periods found for this academic time. Create a new one to get started.
                        </div>
                    @else
                        <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4 text-gray-600 dark:text-gray-300">
                            Select an academic time from the dropdown above to view periods.
                        </div>
                    @endif

                    <!-- Create Button -->
                    <div class="mt-6">
                        <a href="{{ route('periods.create') }}" class="bg-violet-600 hover:bg-violet-700 text-white px-6 py-3 rounded-lg shadow-md transition-colors inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
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
