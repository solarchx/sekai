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

                        <input type="hidden" name="presence_id" value="{{ $presenceId }}">

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Teacher Performance Score</label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="radio" name="score" id="score_0" value="0" {{ old('score') == '0' ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" required>
                                    <label for="score_0" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">0 - Absent</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="score" id="score_1" value="1" {{ old('score') == '1' ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="score_1" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">1 - Mostly absent, did not teach</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="score" id="score_2" value="2" {{ old('score') == '2' ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="score_2" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">2 - Mostly present, barely taught</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="score" id="score_3" value="3" {{ old('score') == '3' ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="score_3" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">3 - Present, taught actively</label>
                                </div>
                            </div>
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