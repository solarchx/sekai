<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Activity Form') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Create New Activity Form</h3>
                    
                    <form method="POST" action="{{ route('activity-forms.store') }}">
                        @csrf

                        <div class="mb-6">
                            <label for="activity_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Activity</label>
                            <select name="activity_id" id="activity_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('activity_id') is-invalid @enderror" required>
                                <option value="">Select Activity</option>
                                @foreach($activities as $activity)
                                    <option value="{{ $activity->id }}" {{ old('activity_id') == $activity->id ? 'selected' : '' }}>{{ $activity->subject->name }} - {{ $activity->teacher->name }} ({{ $activity->class->name }})</option>
                                @endforeach
                            </select>
                            @error('activity_id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="activity_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Activity Date</label>
                            <input type="date" name="activity_date" id="activity_date" value="{{ old('activity_date') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('activity_date') is-invalid @enderror" required>
                            @error('activity_date')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('activity-forms.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Cancel</a>
                            <button type="submit" class="bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
