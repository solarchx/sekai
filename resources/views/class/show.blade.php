<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Class') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(auth()->user()->role === 'STUDENT')
                
                @if($homeroomClasses->isEmpty())
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 text-center text-gray-500">
                        You are not assigned to any class.
                    </div>
                @else
                    @foreach($homeroomClasses as $class)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6">
                            <div class="p-6" style="background: linear-gradient(to right, #6366f1, #3b82f6); color: white;">
                                <h3 class="text-2xl font-bold">{{ $class->name }}</h3>
                                <p class="mt-1">Major: {{ $class->major->name ?? 'N/A' }} | Grade: {{ $class->grade->id ?? 'N/A' }}</p>
                                @if($class->homeroomTeacher)
                                    <p class="mt-1">Homeroom Teacher: {{ $class->homeroomTeacher->name }}</p>
                                @endif
                            </div>
                            <div class="p-6">
                                <h4 class="text-lg font-semibold mb-2">Classmates</h4>
                                @if($class->students->isNotEmpty())
                                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-400">
                                        @foreach($class->students as $student)
                                            <li>{{ $student->name }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-gray-500">No classmates found.</p>
                                @endif

                                <h4 class="text-lg font-semibold mt-6 mb-2">Lessons & Activities</h4>
                                @if($class->activities->isEmpty())
                                    <p class="text-gray-500">No activities found for this class.</p>
                                @else
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full bg-white dark:bg-gray-800">
                                            <thead class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subject</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Teacher</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Schedule</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach($class->activities as $activity)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $activity->subject->name }}</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $activity->teacher->name }}</td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                            {{ $activity->period->weekday_name }} {{ $activity->period->time_begin }}-{{ $activity->period->time_end }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif

            @else
                

                
                @if($homeroomClasses->isNotEmpty())
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Classes I Homeroom</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($homeroomClasses as $class)
                                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                                    <div class="p-6" style="background: linear-gradient(to right, #8b5cf6, #ec4899); color: white;">
                                        <h4 class="text-xl font-bold">{{ $class->name }}</h4>
                                        <p class="text-sm mt-1">Major: {{ $class->major->name ?? 'N/A' }} | Grade: {{ $class->grade->id ?? 'N/A' }}</p>
                                        @if($class->homeroomTeacher)
                                            <p class="text-sm mt-1">Homeroom Teacher: {{ $class->homeroomTeacher->name }}</p>
                                        @endif
                                    </div>
                                    <div class="p-4 flex flex-wrap gap-2">
                                        <a href="{{ route('classes.student-order', $class) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded-lg text-xs inline-flex items-center">
                                            <i class="bi bi-sort-numeric-up mr-1"></i> Student Order
                                        </a>
                                        <button @click="$dispatch('open-modal', 'members-modal-{{ $class->id }}')" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-xs inline-flex items-center">
                                            <i class="bi bi-people mr-1"></i> Members
                                        </button>
                                        <button @click="$dispatch('open-modal', 'activities-modal-{{ $class->id }}')" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-xs inline-flex items-center">
                                            <i class="bi bi-collection mr-1"></i> Activities
                                        </button>
                                    </div>
                                </div>

                                
                                <x-modal name="members-modal-{{ $class->id }}" :show="$errors->isNotEmpty()" focusable>
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                            Members of {{ $class->name }}
                                        </h2>
                                        @if($class->students->isNotEmpty())
                                            <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-1">
                                                @foreach($class->students as $student)
                                                    <li>{{ $student->name }} ({{ $student->identifier ?? 'N/A' }})</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-gray-500">No students in this class.</p>
                                        @endif
                                        <div class="mt-6 flex justify-end">
                                            <x-secondary-button @click="$dispatch('close')">
                                                Close
                                            </x-secondary-button>
                                        </div>
                                    </div>
                                </x-modal>

                                
                                <x-modal name="activities-modal-{{ $class->id }}" :show="$errors->isNotEmpty()" focusable>
                                    <div class="p-6">
                                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                            Activities of {{ $class->name }}
                                        </h2>
                                        @if($class->activities->isNotEmpty())
                                            <table class="min-w-full bg-white dark:bg-gray-800">
                                                <thead class="bg-gray-50 dark:bg-gray-700">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subject</th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Teacher</th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Schedule</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                    @foreach($class->activities as $activity)
                                                        <tr>
                                                            <td class="px-4 py-2 text-sm">{{ $activity->subject->name }}</td>
                                                            <td class="px-4 py-2 text-sm">{{ $activity->teacher->name }}</td>
                                                            <td class="px-4 py-2 text-sm">{{ $activity->period->weekday_name }} {{ $activity->period->time_begin }}-{{ $activity->period->time_end }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <p class="text-gray-500">No activities in this class.</p>
                                        @endif
                                        <div class="mt-6 flex justify-end">
                                            <x-secondary-button @click="$dispatch('close')">
                                                Close
                                            </x-secondary-button>
                                        </div>
                                    </div>
                                </x-modal>
                            @endforeach
                        </div>
                    </div>
                @endif

                
                @if($taughtActivities->isNotEmpty())
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6" style="background: linear-gradient(to right, #f59e0b, #f97316); color: white;">
                            <h3 class="text-2xl font-bold">Activities I Teach</h3>
                            <p class="mt-2">All lessons you are assigned to teach.</p>
                        </div>
                        <div class="p-6 overflow-x-auto">
                            <table class="min-w-full bg-white dark:bg-gray-800">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Class</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subject</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Schedule</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($taughtActivities as $activity)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $activity->class->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $activity->subject->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $activity->period->weekday_name }} {{ $activity->period->time_begin }}-{{ $activity->period->time_end }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                @if (in_array(auth()->user()->role, ['VP', 'ADMIN']))
                                                    <a href="{{ route('activities.show', $activity) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-xs inline-block">Details</a>
                                                    <a href="{{ route('activities.edit', $activity) }}" class="bg-cyan-600 hover:bg-cyan-700 text-white px-3 py-1 rounded-lg text-xs inline-block">Edit</a>
                                                    <form action="{{ route('activities.destroy', $activity) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-xs">Delete</button>
                                                    </form>
                                                @endif
                                                <a href="{{ route('score-distributions.index', $activity) }}" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded-lg text-xs inline-block">Scores Dist</a>
                                                <a href="{{ route('student-scores.index', $activity) }}" class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded-lg text-xs inline-block">Student Scores</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if($homeroomClasses->isEmpty() && $taughtActivities->isEmpty())
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 text-center text-gray-500">
                        You are not assigned as homeroom teacher to any class, and you have no teaching activities.
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>