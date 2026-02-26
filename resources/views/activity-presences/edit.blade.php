<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Presence Record') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Edit Presence Record</h3>
                    
                    <form method="POST" action="{{ route('activity-presences.update', $activityPresence) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label for="form_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Activity Form</label>
                            <select name="form_id" id="form_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('form_id') is-invalid @enderror" required>
                                <option value="">Select Form</option>
                                @foreach($forms as $form)
                                    <option value="{{ $form->id }}" {{ old('form_id', $activityPresence->form_id) == $form->id ? 'selected' : '' }}>
                                        {{ $form->activity->subject->name }} - {{ $form->activity_date }} ({{ $form->activity->class->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('form_id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="student_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Student</label>
                            <select name="student_id" id="student_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('student_id') is-invalid @enderror" required>
                                <option value="">Select Student</option>
                                @php
                                    $currentForm = $forms->firstWhere('id', old('form_id', $activityPresence->form_id));
                                @endphp
                                @if($currentForm && $currentForm->activity->students)
                                    @foreach($currentForm->activity->students as $student)
                                        <option value="{{ $student->id }}" {{ old('student_id', $activityPresence->student_id) == $student->id ? 'selected' : '' }}>
                                            {{ $student->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('student_id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Attendance Status</label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="radio" name="score" id="score_0" value="0" {{ old('score', $activityPresence->score) == '0' ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" required>
                                    <label for="score_0" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">0 - Absent</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="score" id="score_1" value="1" {{ old('score', $activityPresence->score) == '1' ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="score_1" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">1 - Permitted Leave</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="score" id="score_2" value="2" {{ old('score', $activityPresence->score) == '2' ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="score_2" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">2 - Sick Leave</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="score" id="score_3" value="3" {{ old('score', $activityPresence->score) == '3' ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="score_3" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">3 - Present</label>
                                </div>
                            </div>
                            @error('score')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Location (GPS)</label>
                            <input type="text" name="location" id="location" value="{{ old('location', $activityPresence->location) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('location') is-invalid @enderror" required placeholder="e.g., -6.2088, 106.8456">
                            @error('location')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('activity-presences.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Cancel</a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>