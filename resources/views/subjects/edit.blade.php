<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Subject') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Edit Subject</h3>
                    
                    <form method="POST" action="{{ route('subjects.update', $subject) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $subject->name) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('name') is-invalid @enderror" required>
                            @error('name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        
                        <div class="mb-6">
                            <label for="majors" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Applicable Majors</label>
                            <select name="majors[]" id="majors" multiple class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('majors') is-invalid @enderror" size="5">
                                @foreach($majors as $major)
                                    @php
                                        $selected = $subject->majors->contains($major->id) || (is_array(old('majors')) && in_array($major->id, old('majors')));
                                    @endphp
                                    <option value="{{ $major->id }}" {{ $selected ? 'selected' : '' }}>
                                        {{ $major->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('majors')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        
                        <div class="mb-6">
                            <label for="grades" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Applicable Grades</label>
                            <select name="grades[]" id="grades" multiple class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('grades') is-invalid @enderror" size="5">
                                @foreach($grades as $grade)
                                    @php
                                        $selected = $subject->grades->contains($grade->id) || (is_array(old('grades')) && in_array($grade->id, old('grades')));
                                    @endphp
                                    <option value="{{ $grade->id }}" {{ $selected ? 'selected' : '' }}>
                                        Grade {{ $grade->id }}
                                    </option>
                                @endforeach
                            </select>
                            @error('grades')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('subjects.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Cancel</a>
                            <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>