<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Score Distribution') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Edit Score Distribution</h3>
                    
                    <form method="POST" action="{{ route('score-distributions.update', [$scoreDistribution->activity_id, $scoreDistribution->name]) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label for="activity_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Activity</label>
                            <select name="activity_id" id="activity_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('activity_id') is-invalid @enderror" required>
                                <option value="">Select Activity</option>
                                @foreach($activities as $activity)
                                    <option value="{{ $activity->id }}" {{ old('activity_id', $scoreDistribution->activity_id) == $activity->id ? 'selected' : '' }}>{{ $activity->subject->name }} - {{ $activity->teacher->name }} ({{ $activity->class->name }})</option>
                                @endforeach
                            </select>
                            @error('activity_id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Component Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $scoreDistribution->name) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('name') is-invalid @enderror" required>
                            @error('name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Weight</label>
                            <input type="number" name="weight" id="weight" min="1" value="{{ old('weight', $scoreDistribution->weight) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('weight') is-invalid @enderror" required>
                            @error('weight')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('score-distributions.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Cancel</a>
                            <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
