<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Class') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Edit Class') }}</h3>

                    <form action="{{ route('classes.update', $class) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Class Name') }}
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name', $class->name) }}"
                                class="block w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition @error('name') border-red-500 @enderror"
                                placeholder="{{ __('Enter class name') }}" required />
                            @error('name')
                                <span class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="major_id"
                                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Major') }}
                            </label>
                            <select id="major_id" name="major_id"
                                class="block w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition @error('major_id') border-red-500 @enderror"
                                required>
                                <option value="">{{ __('Select a major') }}</option>
                                @foreach($majors as $major)
                                    <option value="{{ $major->id }}" @selected(old('major_id', $class->major_id) == $major->id)>
                                        {{ $major->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('major_id')
                                <span class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="grade_id"
                                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Grade') }}
                            </label>
                            <select id="grade_id" name="grade_id"
                                class="block w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition @error('grade_id') border-red-500 @enderror"
                                required>
                                <option value="">{{ __('Select a grade') }}</option>
                                @foreach($grades as $grade)
                                    <option value="{{ $grade->id }}" @selected(old('grade_id', $class->grade_id) == $grade->id)>
                                        {{ __('Grade') }} {{ $grade->id }}
                                    </option>
                                @endforeach
                            </select>
                            @error('grade_id')
                                <span class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="capacity"
                                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Capacity') }}
                            </label>
                            <input type="number" id="capacity" name="capacity"
                                value="{{ old('capacity', $class->capacity) }}"
                                class="block w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition @error('capacity') border-red-500 @enderror"
                                placeholder="{{ __('Enter classroom capacity') }}" min="1" max="100" required />
                            @error('capacity')
                                <span class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="mb-6">
                            <label for="homeroom_teacher_id"
                                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Homeroom Teacher (optional)') }}
                            </label>
                            <select id="homeroom_teacher_id" name="homeroom_teacher_id"
                                class="block w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition @error('homeroom_teacher_id') border-red-500 @enderror">
                                <option value="">-- {{ __('No homeroom teacher') }} --</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" @selected(old('homeroom_teacher_id', $class->homeroom_teacher_id) == $teacher->id)>
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('homeroom_teacher_id')
                                <span class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex gap-4">
                            <button type="submit"
                                class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200">
                                {{ __('Update Class') }}
                            </button>
                            <a href="{{ route('classes.index') }}"
                                class="flex-1 px-4 py-2.5 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition duration-200 text-center">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
