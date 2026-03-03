@props(['parentPeriods', 'periods', 'isReadOnly' => true])


<div class="overflow-x-auto mb-6">
    <table class="min-w-full bg-white dark:bg-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Period</th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Monday</th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Tuesday</th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Wednesday</th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Thursday</th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Friday</th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Saturday</th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Sunday</th>
                @if(!$isReadOnly)
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Actions</th>
                @endif
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($parentPeriods as $parentPeriod)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td
                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-700">
                        {{ $parentPeriod->time_begin }} - {{ $parentPeriod->time_end }}
                    </td>
                    @php
                        $childPeriods = $periods->filter(fn($p) => $p->parent_id == $parentPeriod->id)->keyBy('weekday');
                    @endphp
                    @for ($day = 0; $day < 7; $day++)
                        <td
                            class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100 border border-gray-200 dark:border-gray-600">
                            @if(isset($childPeriods[$day]))
                                @php
                                    $activities = $childPeriods[$day]->activities ?? collect();
                                @endphp
                                <div class="space-y-1">
                                    @forelse($activities as $activity)
                                        <div
                                            class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded p-2 text-xs">
                                            <div class="font-semibold text-blue-900 dark:text-blue-100">
                                                {{ $activity->subject->name ?? __('N/A') }}
                                            </div>
                                            <div class="text-blue-700 dark:text-blue-300">Teacher:
                                                {{ $activity->teacher->name ?? __('N/A') }}
                                            </div>
                                            <div class="text-blue-700 dark:text-blue-300">Class:
                                                {{ $activity->class->name ?? __('N/A') }}
                                            </div>
                                        </div>
                                    @empty
                                        <span class="text-gray-400">--</span>
                                    @endforelse
                                </div>
                            @else
                                <span class="text-gray-400">--</span>
                            @endif
                        </td>
                    @endfor
                    @if(!$isReadOnly)
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route('periods.edit', $parentPeriod) }}"
                                class="bg-cyan-600 hover:bg-cyan-700 text-white px-3 py-1 rounded-lg text-xs transition-colors inline-block">Edit</a>
                            <form action="{{ route('periods.destroy', $parentPeriod) }}" method="POST" class="inline"
                                onsubmit="return confirm('{{ __('Are you sure? All 7 day periods will be deleted.') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-xs transition-colors">Delete</button>
                            </form>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $isReadOnly ? 8 : 9 }}" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                        No activities scheduled.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>