<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Presence Record') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Create New Presence Record</h3>
                    
                    <form method="POST" action="{{ route('activity-presences.store') }}">
                        @csrf

                        <div class="mb-6">
                            <label for="form_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Activity Form</label>
                            <select name="form_id" id="form_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('form_id') is-invalid @enderror" required>
                                <option value="">Select Form</option>
                                @foreach($forms as $form)
                                    <option value="{{ $form->id }}" {{ old('form_id') == $form->id ? 'selected' : '' }}>{{ $form->activity->subject->name }} - {{ $form->activity_date }}</option>
                                @endforeach
                            </select>
                            @error('form_id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="student_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Student</label>
                            <select name="student_id" id="student_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('student_id') is-invalid @enderror" required>
                                <option value="">Select Student</option>
                            </select>
                            @error('student_id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="score" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Score (0-100)</label>
                            <input type="number" name="score" id="score" min="0" max="100" value="{{ old('score') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('score') is-invalid @enderror" required>
                            @error('score')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('activity-presences.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Cancel</a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('form_id').addEventListener('change', function() {
            const formId = this.value;
            const studentSelect = document.getElementById('student_id');
            studentSelect.innerHTML = '<option value="">Select Student</option>';
            
            if (!formId) return;

            // Find the selected form and populate students
            const forms = @json($forms);
            const selectedForm = forms.find(f => f.id == formId);
            
            if (selectedForm && selectedForm.activity.students) {
                selectedForm.activity.students.forEach(student => {
                    const option = document.createElement('option');
                    option.value = student.id;
                    option.textContent = student.name;
                    studentSelect.appendChild(option);
                });
            }
        });
    </script>
</x-app-layout>
