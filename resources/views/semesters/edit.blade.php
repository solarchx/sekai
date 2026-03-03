<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Semester') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">{{ __('Edit Semester') }}</h3>

                    <form method="POST" action="{{ route('semesters.update', $semester) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label for="academic_year"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Academic Year</label>
                            <input type="text" name="academic_year" id="academic_year" placeholder="e.g., 2023-2024"
                                value="{{ old('academic_year', $semester->academic_year) }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('academic_year') is-invalid @enderror"
                                required>
                            @error('academic_year')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="semester"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Semester</label>
                            <select name="semester" id="semester"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('semester') is-invalid @enderror"
                                required>
                                <option value="">Select Semester</option>
                                <option value="1" {{ old('semester', $semester->semester) == '1' ? 'selected' : '' }}>1
                                </option>
                                <option value="2" {{ old('semester', $semester->semester) == '2' ? 'selected' : '' }}>2
                                </option>
                            </select>
                            @error('semester')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('semesters.index') }}"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Cancel</a>
                            <button type="submit"
                                class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>