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
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                        {{ __('Create New Presence Record') }}</h3>

                    <form method="POST" action="{{ route('activity-presences.store') }}" id="presenceForm">
                        @csrf

                        <input type="hidden" name="form_id" value="{{ $form->id }}">
                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                        <input type="hidden" name="location" id="location" value="">

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Activity
                                Form</label>
                            <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">
                                {{ $form->activity->subject->name }} - {{ $form->activity_date }}
                                ({{ $form->activity->class->name }})</p>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Student</label>
                            <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ $student->name }}</p>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Attendance
                                Status</label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="radio" name="score" id="score_0" value="0" {{ old('score') == '0' ? 'checked' : '' }}
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" required>
                                    <label for="score_0"
                                        class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">0 -
                                        Absent</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="score" id="score_1" value="1" {{ old('score') == '1' ? 'checked' : '' }}
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="score_1"
                                        class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">1 -
                                        Permitted Leave</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="score" id="score_2" value="2" {{ old('score') == '2' ? 'checked' : '' }}
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="score_2"
                                        class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">2 - Sick
                                        Leave</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="score" id="score_3" value="3" {{ old('score') == '3' ? 'checked' : '' }}
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <label for="score_3"
                                        class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">3 -
                                        Present</label>
                                </div>
                            </div>
                            @error('score')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('class.show') }}"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Cancel</a>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors"
                                id="submitBtn">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const locationInput = document.getElementById('location');
            const submitBtn = document.getElementById('submitBtn');
            const form = document.getElementById('presenceForm');

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        locationInput.value = position.coords.latitude + ',' + position.coords.longitude;
                    },
                    function (error) {
                        alert('Unable to retrieve your location. Please enable location services and try again.');
                        submitBtn.disabled = true;
                    }
                );
            } else {
                alert('Geolocation is not supported by your browser.');
                submitBtn.disabled = true;
            }

            form.addEventListener('submit', function (e) {
                if (!locationInput.value) {
                    e.preventDefault();
                    alert('Location is required. Please enable location services.');
                }
            });
        });
    </script>
</x-app-layout>