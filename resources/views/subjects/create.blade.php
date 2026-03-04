<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Subject') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">{{ __('Create New Subject') }}
                    </h3>

                    <form method="POST" action="{{ route('subjects.store') }}" id="subjectForm">
                        @csrf

                        <div class="mb-6">
                            <label for="name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Name') }}</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('name') is-invalid @enderror"
                                required>
                            @error('name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Availability (select major, then check grades)') }}
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border border-gray-300 dark:border-gray-600 rounded-lg p-4">
                                <div>
                                    <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Majors') }}</h4>
                                    <div class="space-y-2 max-h-96 overflow-y-auto pr-2">
                                        @foreach($majors as $major)
                                            <div class="flex items-center">
                                                <input type="checkbox" id="major_{{ $major->id }}" value="{{ $major->id }}"
                                                    class="major-checkbox focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                                <label for="major_{{ $major->id }}" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $major->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div id="grades-panel">
                                    <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Grades for selected major') }}</h4>
                                    <div id="grades-container" class="space-y-2 max-h-96 overflow-y-auto pr-2 bg-gray-50 dark:bg-gray-700 p-3 rounded">
                                        <p class="text-gray-500 dark:text-gray-400 text-sm italic">{{ __('Select a major on the left to see grades.') }}</p>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="combinations" id="combinations-input" value="">

                            @error('combinations')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('subjects.index') }}"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">{{ __('Cancel') }}</a>
                            <button type="submit"
                                class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">{{ __('Create') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const majors = @json($majors);
            const allGrades = @json($grades);
            const majorCheckboxes = document.querySelectorAll('.major-checkbox');
            const gradesContainer = document.getElementById('grades-container');
            const combinationsInput = document.getElementById('combinations-input');

            let selectedCombinations = {};
            majors.forEach(major => {
                selectedCombinations[major.id] = {};
                allGrades.forEach(grade => {
                    selectedCombinations[major.id][grade.id] = false;
                });
            });

            let selectedMajorId = null;

            function renderGradesForMajor(majorId) {
                const major = majors.find(m => m.id == majorId);
                if (!major) return;

                let html = `<h5 class="font-medium text-gray-800 dark:text-gray-200 mb-2">{{ __('Major') }}: ${major.name}</h5>`;
                allGrades.forEach(grade => {
                    const gradeId = grade.id;
                    const checked = selectedCombinations[majorId][gradeId] ? 'checked' : '';
                    html += `
                        <div class="flex items-center">
                            <input type="checkbox" id="grade_${majorId}_${gradeId}" data-major="${majorId}" data-grade="${gradeId}" class="grade-checkbox focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" ${checked}>
                            <label for="grade_${majorId}_${gradeId}" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                                {{ __('Grade') }} ${gradeId}
                            </label>
                        </div>
                    `;
                });
                gradesContainer.innerHTML = html;

                document.querySelectorAll('.grade-checkbox').forEach(cb => {
                    cb.addEventListener('change', function() {
                        const major = parseInt(this.dataset.major);
                        const grade = parseInt(this.dataset.grade);
                        selectedCombinations[major][grade] = this.checked;
                    });
                });
            }

            majorCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        majorCheckboxes.forEach(cb => {
                            if (cb !== this) cb.checked = false;
                        });
                        selectedMajorId = this.value;
                        renderGradesForMajor(selectedMajorId);
                    } else {
                        if (selectedMajorId === this.value) {
                            selectedMajorId = null;
                            gradesContainer.innerHTML = `<p class="text-gray-500 dark:text-gray-400 text-sm italic">{{ __('Select a major on the left to see grades.') }}</p>`;
                        }
                    }
                });
            });

            document.getElementById('subjectForm').addEventListener('submit', function(e) {
                combinationsInput.value = JSON.stringify(selectedCombinations);
            });
        });
    </script>
</x-app-layout>