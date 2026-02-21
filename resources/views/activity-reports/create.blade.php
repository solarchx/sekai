<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Activity Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Create New Activity Report</h3>
                    
                    <form method="POST" action="{{ route('activity-reports.store') }}">
                        @csrf

                        <div class="mb-6">
                            <label for="presence_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Presence</label>
                            <select name="presence_id" id="presence_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('presence_id') is-invalid @enderror" required>
                                <option value="">Select Presence</option>
                                @foreach($presences as $presence)
                                    <option value="{{ $presence->id }}" {{ old('presence_id') == $presence->id ? 'selected' : '' }}>{{ $presence->student->name }} - {{ $presence->form->activity_date }}</option>
                                @endforeach
                            </select>
                            @error('presence_id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="score" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Score (0-100)</label>
                            <input type="number" name="score" id="score" min="0" max="100" value="{{ old('score') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('score') is-invalid @enderror" required>
                            @error('score')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="topic" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Topic</label>
                            <input type="text" name="topic" id="topic" value="{{ old('topic') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('topic') is-invalid @enderror" required>
                            @error('topic')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="details" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Details</label>
                            <textarea name="details" id="details" rows="4" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('details') is-invalid @enderror" required>{{ old('details') }}</textarea>
                            @error('details')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('activity-reports.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Cancel</a>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
