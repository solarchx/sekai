<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Create Class') }}
            </h2>
            <x-admin-tabs active="classes" />
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Add New Class</h3>
                    
                    <form action="{{ route('classes.store') }}" method="POST">
                        @csrf

                        <div class="mb-6">
                            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Class Name
                            </label>
                            <input 
                                type="text" 
                                id="name"
                                name="name" 
                                value="{{ old('name') }}"
                                class="block w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition @error('name') border-red-500 @enderror"
                                placeholder="Enter class name"
                                required
                            />
                            @error('name')
                                <span class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="major_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Major
                            </label>
                            <select 
                                id="major_id"
                                name="major_id"
                                class="block w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition @error('major_id') border-red-500 @enderror"
                                required
                            >
                                <option value="">Select a major</option>
                                @foreach($majors as $major)
                                    <option value="{{ $major->id }}" @selected(old('major_id') == $major->id)>
                                        {{ $major->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('major_id')
                                <span class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="grade_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Grade
                            </label>
                            <select 
                                id="grade_id"
                                name="grade_id"
                                class="block w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition @error('grade_id') border-red-500 @enderror"
                                required
                            >
                                <option value="">Select a grade</option>
                                @foreach($grades as $grade)
                                    <option value="{{ $grade->id }}" @selected(old('grade_id') == $grade->id)>
                                        Grade {{ $grade->id }}
                                    </option>
                                @endforeach
                            </select>
                            @error('grade_id')
                                <span class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="capacity" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Capacity
                            </label>
                            <input 
                                type="number" 
                                id="capacity"
                                name="capacity" 
                                value="{{ old('capacity') }}"
                                class="block w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition @error('capacity') border-red-500 @enderror"
                                placeholder="Enter classroom capacity"
                                min="1"
                                max="100"
                                required
                            />
                            @error('capacity')
                                <span class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex gap-4">
                            <button 
                                type="submit" 
                                class="flex-1 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition duration-200"
                            >
                                Create Class
                            </button>
                            <a 
                                href="{{ route('classes.index') }}" 
                                class="flex-1 px-4 py-2.5 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition duration-200 text-center"
                            >
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
