<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit User') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6" style="background: linear-gradient(to right, #6366f1, #3b82f6); color: white;">
                    <h3 class="text-2xl font-bold">Edit User</h3>
                    <p class="mt-2">Edit the user account with the required details.</p>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div>
                                <label for="identifier" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Identifier</label>
                                <input type="text" name="identifier" id="identifier" value="{{ old('identifier', $user->identifier) }}" required 
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                <x-input-error :messages="$errors->get('identifier')" class="mt-2" />
                            </div>
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role</label>
                                <select name="role" id="role" required 
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                    <option value="STUDENT" {{ old('role', $user->role) == 'STUDENT' ? 'selected' : '' }}>Student</option>
                                    <option value="TEACHER" {{ old('role', $user->role) == 'TEACHER' ? 'selected' : '' }}>Teacher</option>
                                    <option value="VP" {{ old('role', $user->role) == 'VP' ? 'selected' : '' }}>VP</option>
                                    <option value="ADMIN" {{ old('role', $user->role) == 'ADMIN' ? 'selected' : '' }}>Admin</option>
                                </select>
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            </div>
                            @if ($user->role === 'STUDENT')
                                <div>
                                    <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Class</label>
                                    <select name="class_id" id="class_id" 
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                        <option value="">Select Class</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('class_id', $user->class_id) == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }} - ({{ $class->grade->id }} {{ $class->major->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('class_id')" class="mt-2" />
                                </div>
                            @endif

                            @if($user->role === 'STUDENT')
                                <div id="activity-section" style="{{ $user->class_id ? '' : 'display: none;' }}">
                                    <label for="activity_ids" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Enrolled Activities
                                    </label>
                                    <select name="activity_ids[]" id="activity_ids" multiple 
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white" size="6">
                                        @foreach($classActivities as $activity)
                                            <option value="{{ $activity->id }}" 
                                                {{ in_array($activity->id, old('activity_ids', $enrolledActivityIds)) ? 'selected' : '' }}>
                                                {{ $activity->subject->name }} – {{ $activity->period->weekday_name }} {{ $activity->period->time_begin }}-{{ $activity->period->time_end }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Hold Ctrl/Cmd to select multiple.</p>
                                    <x-input-error :messages="$errors->get('activity_ids')" class="mt-2" />
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('users.index') }}" class="mr-4 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white">Cancel</a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">
                                Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($user->role === 'STUDENT')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const classSelect = document.getElementById('class_id');
            const activitySection = document.getElementById('activity-section');
            const activitySelect = document.getElementById('activity_ids');
            const allActivities = @json($classActivities);

            function updateActivityOptions() {
                const classId = classSelect.value;
                if (!classId) {
                    activitySection.style.display = 'none';
                    return;
                }
                if (classId != {{ $user->class_id ?? 'null' }}) {
                    alert('Changing the class will reset the activity enrollment to all activities of the new class. Please save the form to apply the change.');
                }
                activitySection.style.display = 'block';
            }

            classSelect.addEventListener('change', updateActivityOptions);
        });
    </script>
    @endif
</x-app-layout>