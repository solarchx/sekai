<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Lesson Period') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Create New Lesson Period</h3>
                    
                    <form method="POST" action="{{ route('periods.store') }}">
                        @csrf

                        <div class="mb-6">
                            <label for="weekday" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Weekday</label>
                            <select name="weekday" id="weekday" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('weekday') is-invalid @enderror" required>
                                <option value="">Select Weekday</option>
                                <option value="0" {{ old('weekday') == '0' ? 'selected' : '' }}>Sunday</option>
                                <option value="1" {{ old('weekday') == '1' ? 'selected' : '' }}>Monday</option>
                                <option value="2" {{ old('weekday') == '2' ? 'selected' : '' }}>Tuesday</option>
                                <option value="3" {{ old('weekday') == '3' ? 'selected' : '' }}>Wednesday</option>
                                <option value="4" {{ old('weekday') == '4' ? 'selected' : '' }}>Thursday</option>
                                <option value="5" {{ old('weekday') == '5' ? 'selected' : '' }}>Friday</option>
                                <option value="6" {{ old('weekday') == '6' ? 'selected' : '' }}>Saturday</option>
                            </select>
                            @error('weekday')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="time_begin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Time</label>
                            <input type="time" name="time_begin" id="time_begin" value="{{ old('time_begin') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('time_begin') is-invalid @enderror" required>
                            @error('time_begin')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="time_end" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Time</label>
                            <input type="time" name="time_end" id="time_end" value="{{ old('time_end') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('time_end') is-invalid @enderror" required>
                            @error('time_end')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="semester_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Semester</label>
                            <select name="semester_id" id="semester_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('semester_id') is-invalid @enderror" required>
                                <option value="">Select Semester</option>
                                @foreach($semesters as $semester)
                                    <option value="{{ $semester->id }}" {{ old('semester_id') == $semester->id ? 'selected' : '' }}>{{ $semester->full_name }}</option>
                                @endforeach
                            </select>
                            @error('semester_id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('periods.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Cancel</a>
                            <button type="submit" class="bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
