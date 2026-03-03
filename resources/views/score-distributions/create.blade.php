<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ isset($distributions) ? 'Edit' : 'Create' }} {{ __('Score Distributions') }} {{ __('for') }} {{ $activity->subject->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                        {{ isset($distributions) ? 'Edit Distributions' : 'Add Distributions' }}
                    </h3>
                    <p class="mb-4 text-gray-600 dark:text-gray-400">
                        {{ __('Activity') }}: {{ $activity->subject->name }} ({{ $activity->class->name }})
                    </p>

                    <form method="POST" action="{{ route('score-distributions.store', $activity) }}"
                        id="distributionForm">
                        @csrf

                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Distributions') }}</label>
                                <button type="button" onclick="addRow()"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-sm">
                                    {{ __('+') }} {{ __('Add Row') }}
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table
                                    class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-2 border">{{ __('Name') }}</th>
                                            <th class="px-4 py-2 border">{{ __('Weight (%)') }}</th>
                                            <th class="px-4 py-2 border">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="distributions-body">
                                        @if(isset($distributions) && $distributions->count() > 0)
                                            @foreach($distributions as $index => $dist)
                                                <tr class="distribution-row">
                                                    <td class="px-4 py-2 border">
                                                        <input type="text" name="distributions[{{ $index }}][name]"
                                                            value="{{ $dist->name }}"
                                                            class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                            required>
                                                    </td>
                                                    <td class="px-4 py-2 border">
                                                        <input type="number" name="distributions[{{ $index }}][weight]"
                                                            value="{{ $dist->weight }}" min="1" max="100"
                                                            class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                            required>
                                                    </td>
                                                    <td class="px-4 py-2 border text-center">
                                                        <button type="button" onclick="removeRow(this)"
                                                            class="text-red-600 hover:text-red-800">{{ __('Delete') }}</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                {{ __('Total weight must sum to 100.') }}</p>
                            @error('distributions')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('score-distributions.index', $activity) }}"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">{{ __('Cancel') }}</a>
                            <button type="submit"
                                class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">
                                {{ isset($distributions) ? __('Update') : __('Save') }} {{ __('Distributions') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let rowIndex = {{ isset($distributions) ? $distributions->count() : 0 }};

        function addRow() {
            const tbody = document.getElementById('distributions-body');
            const newRow = document.createElement('tr');
            newRow.className = 'distribution-row';
            newRow.innerHTML = `
                <td class="px-4 py-2 border">
                    <input type="text" name="distributions[${rowIndex}][name]" class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required>
                </td>
                <td class="px-4 py-2 border">
                    <input type="number" name="distributions[${rowIndex}][weight]" min="1" max="100" class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required>
                </td>
                <td class="px-4 py-2 border text-center">
                    <button type="button" onclick="removeRow(this)" class="text-red-600 hover:text-red-800">{{ __('Delete') }}</button>
                </td>
            `;
            tbody.appendChild(newRow);
            rowIndex++;
        }

        function removeRow(button) {
            const row = button.closest('tr');
            if (document.querySelectorAll('.distribution-row').length > 1) {
                row.remove();
            } else {
                alert('At least one distribution is required.');
            }
        }
    </script>
</x-app-layout>
