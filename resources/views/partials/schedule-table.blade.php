@if(isset($schedule) && count($schedule) > 0)
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mt-6">
        <div class="p-6" style="background: linear-gradient(to right, #06b6d4, #0891b2); color: white;">
            <h3 class="text-2xl font-bold">My Schedule</h3>
            <p class="mt-2">Your upcoming classes and activities.</p>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 border">Day</th>
                        <th class="px-4 py-2 border">Time</th>
                        <th class="px-4 py-2 border">Subject</th>
                        <th class="px-4 py-2 border">Teacher</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedule as $weekday => $activities)
                        @foreach($activities as $activity)
                            @if ($activity->period) 
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2 border">{{ $activity->period->weekday_name }}</td>
                                    <td class="px-4 py-2 border">{{ $activity->period->time_begin }} - {{ $activity->period->time_end }}</td>
                                    <td class="px-4 py-2 border">{{ $activity->subject->name }}</td>
                                    <td class="px-4 py-2 border">{{ $activity->teacher->name }}</td>
                                    <td class="px-4 py-2 border">{{ $activity->class->name }}</td>
                                    @if (auth()->user()->role !== 'STUDENT')
                                        <td class="px-4 py-2 border">{{ $activity->period->semester->full_name }}</td>
                                    @endif
                                </tr>
                            @else
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2 border">??????</td>
                                    <td class="px-4 py-2 border">??:??:?? - ??:??:??</td>
                                    <td class="px-4 py-2 border">{{ $activity->subject->name }}</td>
                                    <td class="px-4 py-2 border">{{ $activity->teacher->name }}</td>
                                    <td class="px-4 py-2 border">{{ $activity->class->name }}</td>
                                    @if (auth()->user()->role !== 'STUDENT')
                                        <td class="px-4 py-2 border">??????</td>
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
            <h3 class="text-2xl font-bold">My Schedule</h3>
            <p class="mt-2">Your upcoming classes and activities.</p>
        </div>
        <div class="p-6 overflow-x-auto">
            You have no upcoming class or activity.
        </div>
    </div>
@endif