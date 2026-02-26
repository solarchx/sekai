<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Class') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($userClass)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6" style="background: linear-gradient(to right, #6366f1, #3b82f6); color: white;">
                        <h3 class="text-2xl font-bold">{{ $userClass->name }}</h3>
                        <p class="mt-1">Major: {{ $userClass->major->name ?? 'N/A' }} | Grade: {{ $userClass->grade->id ?? 'N/A' }}</p>
                        @if($homeroomTeacher)
                            <p class="mt-1">Homeroom Teacher: {{ $homeroomTeacher->name }}</p>
                        @endif
                    </div>
                    <div class="p-6">
                        @if(auth()->user()->role === 'STUDENT' && $students->isNotEmpty())
                            <h4 class="text-lg font-semibold mb-2">Classmates</h4>
                            <ul class="list-disc list-inside text-gray-600 dark:text-gray-400">
                                @foreach($students as $student)
                                    <li>{{ $student->name }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <!-- Activities List -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6" style="background: linear-gradient(to right, #6366f1, #3b82f6); color: white;">
                        <h3 class="text-2xl font-bold">Lessons & Activities</h3>
                        <p class="mt-2">Activities scheduled for this class.</p>
                    </div>
                    <div class="p-6">
                        @if($activities->isEmpty())
                            <p class="text-gray-600 dark:text-gray-400">No activities found for this class.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white dark:bg-gray-800">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subject</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Teacher</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Schedule</th>
                                            @if(auth()->user()->role !== 'STUDENT')
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($activities as $activity)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $activity->subject->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $activity->teacher->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $activity->period->weekday_name }} {{ $activity->period->time_begin }}-{{ $activity->period->time_end }}
                                                </td>
                                                @if(auth()->user()->role !== 'STUDENT')
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                        <a href="{{ route('activities.show', $activity) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-xs">Details</a>
                                                        <a href="{{ route('activities.edit', $activity) }}" class="bg-cyan-600 hover:bg-cyan-700 text-white px-3 py-1 rounded-lg text-xs">Edit</a>
                                                        <form action="{{ route('activities.destroy', $activity) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-xs">Delete</button>
                                                        </form>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 text-center text-gray-500">
                    You are not assigned to any class.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>