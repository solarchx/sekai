@if(isset($schedule) && count($schedule) > 0)
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mt-6">
        <div class="p-6" style="background: linear-gradient(to right, #06b6d4, #0891b2); color: white;">
            <h3 class="text-2xl font-bold">{{ __('My Schedule') }}</h3>
            <p class="mt-2">{{ __('Your upcoming classes and activities.') }}</p>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Day') }}</th>
                        <th class="px-4 py-2 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Time') }}</th>
                        <th class="px-4 py-2 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Subject') }}</th>
                        <th class="px-4 py-2 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Teacher') }}</th>
                        <th class="px-4 py-2 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Class') }}</th>
                        @if (auth()->user()->role !== 'STUDENT')
                            <th class="px-4 py-2 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Semester') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedule as $weekday => $activities)
                        @foreach($activities as $activity)
                            @if ($activity->period)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2">{{ $activity->period->weekday_name }}</td>
                                    <td class="px-4 py-2">{{ $activity->period->time_begin }} - {{ $activity->period->time_end }}</td>
                                    <td class="px-4 py-2">{{ $activity->subject->name }}</td>
                                    <td class="px-4 py-2">{{ $activity->teacher->name }}</td>
                                    <td class="px-4 py-2">{{ $activity->class->name }}</td>
                                    @if (auth()->user()->role !== 'STUDENT')
                                        <td class="px-4 py-2">{{ $activity->period->semester->full_name }}</td>
                                    @endif
                                </tr>
                            @else
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2">??????</td>
                                    <td class="px-4 py-2">??:??:?? - ??:??:??</td>
                                    <td class="px-4 py-2">{{ $activity->subject->name }}</td>
                                    <td class="px-4 py-2">{{ $activity->teacher->name }}</td>
                                    <td class="px-4 py-2">{{ $activity->class->name }}</td>
                                    @if (auth()->user()->role !== 'STUDENT')
                                        <td class="px-4 py-2">??????</td>
                                    @endif
                                </tr>
                            @endif
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mt-6">
        <div class="p-6" style="background: linear-gradient(to right, #06b6d4, #0891b2); color: white;">
            <h3 class="text-2xl font-bold">{{ __('My Schedule') }}</h3>
            <p class="mt-2">{{ __('Your upcoming classes and activities.') }}</p>
        </div>
        <div class="p-6 overflow-x-auto">
            {{ __('You have no upcoming class or activity.') }}
        </div>
    </div>
@endif
