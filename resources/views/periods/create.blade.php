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
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                        {{ __('Create New Lesson Period') }}</h3>

                    <form method="POST" action="{{ route('periods.store') }}">
                        @csrf


                        <input type="hidden" name="semester_id" value="{{ request('semester_id') }}">

                        <div class="mb-6">
                            <label for="time_begin"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Time</label>
                            <input type="time" name="time_begin" id="time_begin" value="{{ old('time_begin') }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('time_begin') is-invalid @enderror"
                                required>
                            @error('time_begin')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="time_end" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End
                                Time</label>
                            <input type="time" name="time_end" id="time_end" value="{{ old('time_end') }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('time_end') is-invalid @enderror"
                                required>
                            @error('time_end')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('periods.index', ['semester_id' => request('semester_id')]) }}"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Cancel</a>
                            <button type="submit"
                                class="bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>