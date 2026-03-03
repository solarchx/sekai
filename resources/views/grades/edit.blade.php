<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Grade') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">{{ __('Edit Grade') }}</h3>

                    <form method="POST" action="{{ route('grades.update', $grade) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label for="id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Grade
                                ID</label>
                            <input type="number" name="id" id="id" value="{{ old('id', $grade->id) }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('id') is-invalid @enderror"
                                required>
                            @error('id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('grades.index') }}"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Cancel</a>
                            <button type="submit"
                                class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>