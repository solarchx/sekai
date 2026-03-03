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
                    <h3 class="text-2xl font-bold">{{ __('Lesson Period Schedule Sheet') }}</h3>
                    <p class="mt-2">{{ __('View and manage lesson periods by academic time, major, and grade.') }}</p>
                </div>
                <div class="p-6">
                    {{-- Filter row --}}
                    <form method="GET" action="{{ route('periods.index') }}" id="filter-form">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div>
                                <label for="semester_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('Semester') }}
                                </label>
                                <select name="semester_id" id="semester_id" class="mt-1 block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                        onchange="this.form.submit()">
                                    <option value="">-- {{ __('Select Semester') }} --</option>
                                    @foreach($semesters as $semester)
                                        <option value="{{ $semester->id }}" {{ $selectedSemesterId == $semester->id ? 'selected' : '' }}>
                                            {{ $semester->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if(in_array(auth()->user()->role, ['VP', 'ADMIN']))
                                <div>
                                    <label for="major_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('Major') }}
                                    </label>
                                    <select name="major_id" id="major_id" class="mt-1 block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                            onchange="this.form.submit()">
                                        <option value="">-- {{ __('Select Major') }} --</option>
                                        @foreach($majors as $major)
                                            <option value="{{ $major->id }}" {{ $selectedMajorId == $major->id ? 'selected' : '' }}>
                                                {{ $major->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="grade_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('Grade') }}
                                    </label>
                                    <select name="grade_id" id="grade_id" class="mt-1 block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                            onchange="this.form.submit()">
                                        <option value="">-- {{ __('Select Grade') }} --</option>
                                        @foreach($grades as $grade)
                                            <option value="{{ $grade->id }}" {{ $selectedGradeId == $grade->id ? 'selected' : '' }}>
                                                {{ __('Grade') }} {{ $grade->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                {{-- For teachers, they might only see periods for their own major/grade? But we can just hide the filters. --}}
                            @endif

                            @if($selectedSemesterId && $selectedMajorId && $selectedGradeId)
                                <div class="flex items-end">
                                    <a href="{{ route('periods.create', ['semester_id' => $selectedSemesterId, 'major_id' => $selectedMajorId, 'grade_id' => $selectedGradeId]) }}"
                                        class="bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors inline-flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        {{ __('Add New Period') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </form>

                    @if($selectedSemesterId && $selectedMajorId && $selectedGradeId && $parentPeriods->count() > 0)
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border">
                                            {{ __('Time') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border">
                                            {{ __('Monday') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border">
                                            {{ __('Tuesday') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border">
                                            {{ __('Wednesday') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border">
                                            {{ __('Thursday') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border">
                                            {{ __('Friday') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border">
                                            {{ __('Saturday') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border">
                                            {{ __('Sunday') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border">
                                            {{ __('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($parentPeriods as $parentPeriod)
                                        @php
                                            $isTrashed = $parentPeriod->trashed();
                                            $childPeriods = $periods->where('parent_id', $parentPeriod->id)->keyBy('weekday');
                                        @endphp

                                        @if($isTrashed)
                                            <tr class="bg-red-50 dark:bg-red-900 hover:bg-red-100 dark:hover:bg-red-800">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 border">
                                                    {{ $parentPeriod->time_begin }} - {{ $parentPeriod->time_end }}
                                                </td>
                                                <td colspan="7" class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 border text-center">
                                                    <em>{{ __('This period has been deleted.') }}</em>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border">
                                                    <form action="{{ route('periods.restore', $parentPeriod) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-xs transition-colors">
                                                            {{ __('Restore') }}
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @else
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 border">
                                                    {{ $parentPeriod->time_begin }} - {{ $parentPeriod->time_end }}
                                                </td>
                                                @for ($day = 0; $day < 7; $day++)
                                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 border align-top">
                                                        @if(isset($childPeriods[$day]))
                                                            @php
                                                                $period = $childPeriods[$day];
                                                                $periodActivities = $activities->get($period->id, collect());
                                                            @endphp
                                                            @forelse($periodActivities as $activity)
                                                                <div class="mb-2 p-2 bg-blue-50 dark:bg-blue-900 rounded border border-blue-200 dark:border-blue-700">
                                                                    <div class="font-semibold">{{ $activity->class->name }}: {{ $activity->subject->name }}</div>
                                                                    <div class="text-xs">{{ $activity->teacher->name }}</div>
                                                                </div>
                                                            @empty
                                                                <span class="text-gray-400">—</span>
                                                            @endforelse
                                                        @else
                                                            <span class="text-gray-400">—</span>
                                                        @endif
                                                    </td>
                                                @endfor
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium border space-x-2">
                                                    <a href="{{ route('periods.edit', $parentPeriod) }}" class="bg-cyan-600 hover:bg-cyan-700 text-white px-3 py-1 rounded-lg text-xs transition-colors inline-block">
                                                        {{ __('Edit') }}
                                                    </a>
                                                    <form action="{{ route('periods.destroy', $parentPeriod) }}" method="POST" class="inline"
                                                          onsubmit="return confirmDelete('{{ $parentPeriod->hasActivities ? 'true' : 'false' }}', '{{ $parentPeriod->time_begin }}', '{{ $parentPeriod->time_end }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-xs transition-colors">
                                                            {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif($selectedSemesterId && $selectedMajorId && $selectedGradeId)
                        <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 text-blue-700 dark:text-blue-100">
                            {{ __('No periods found for this selection. Click "Add New Period" to create one.') }}
                        </div>
                    @else
                        <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4 text-gray-600 dark:text-gray-300">
                            {{ __('Please select a semester, major, and grade from the dropdowns above.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function filterPeriods() {
            document.getElementById('filter-form').submit();
        }

        function confirmDelete(hasActivities, timeBegin, timeEnd) {
            if (hasActivities === 'true') {
                return confirm(`{{ __('The period :timeBegin–:timeEnd has scheduled activities. Deleting it will remove those activities. Are you absolutely sure?', ['timeBegin' => '${timeBegin}', 'timeEnd' => '${timeEnd}']) }}`);
            }
            return confirm('{{ __('Are you sure you want to delete this period?') }}');
        }
    </script>
</x-app-layout>