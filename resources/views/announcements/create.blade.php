<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Announcement') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form method="POST" action="{{ route('announcements.store') }}">
                    @csrf

                    <div class="mb-6">
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('title') is-invalid @enderror" required>
                        @error('title')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="subtitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subtitle</label>
                        <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('subtitle') is-invalid @enderror" required>
                        @error('subtitle')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Content</label>
                        <textarea name="content" id="content" rows="6" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('content') is-invalid @enderror" required>{{ old('content') }}</textarea>
                        @error('content')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="scope" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Scope</label>
                        <select name="scope" id="scope" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('scope') is-invalid @enderror" onchange="updateScopeOptions()" required>
                            <option value="">Select Scope</option>
                            <option value="PUBLIC" {{ old('scope') == 'PUBLIC' ? 'selected' : '' }}>Public (Everyone)</option>
                            <option value="TEACHERS" {{ old('scope') == 'TEACHERS' ? 'selected' : '' }}>Teachers Only</option>
                            <option value="CLASS-TAUGHT" {{ old('scope') == 'CLASS-TAUGHT' ? 'selected' : '' }}>My Classes</option>
                            <option value="SPECIFIC-CLASS" {{ old('scope') == 'SPECIFIC-CLASS' ? 'selected' : '' }}>Specific Class</option>
                            <option value="SPECIFIC-GRADE" {{ old('scope') == 'SPECIFIC-GRADE' ? 'selected' : '' }}>Specific Grade</option>
                        </select>
                        @error('scope')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="activity-selector" class="mb-6 hidden">
                        <label for="activity_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Class (Activity)</label>
                        <select name="activity_id" id="activity_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Class</option>
                            @foreach($activities as $activity)
                                <option value="{{ $activity->id }}" {{ old('activity_id') == $activity->id ? 'selected' : '' }}>
                                    {{ $activity->subject->name }} – {{ $activity->class->name }} ({{ $activity->period->weekday_name }} {{ $activity->period->time_begin }}-{{ $activity->period->time_end }})
                                </option>
                            @endforeach
                        </select>
                        @error('activity_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="grade-selector" class="mb-6 hidden">
                        <label for="grade_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Grade</label>
                        <select name="grade_id" id="grade_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Grade</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade->id }}" {{ old('grade_id') == $grade->id ? 'selected' : '' }}>Grade {{ $grade->id }}</option>
                            @endforeach
                        </select>
                        @error('grade_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-4">
                        <a href="{{ route('announcements.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Cancel</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateScopeOptions() {
            const scope = document.getElementById('scope').value;
            const activitySelector = document.getElementById('activity-selector');
            const gradeSelector = document.getElementById('grade-selector');

            activitySelector.classList.add('hidden');
            gradeSelector.classList.add('hidden');

            if (scope === 'SPECIFIC-CLASS') {
                activitySelector.classList.remove('hidden');
            } else if (scope === 'SPECIFIC-GRADE') {
                gradeSelector.classList.remove('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', updateScopeOptions);
    </script>
</x-app-layout>