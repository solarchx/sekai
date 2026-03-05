<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('User Management') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6" style="background: linear-gradient(to right, #6366f1, #3b82f6); color: white;">
                    <h3 class="text-2xl font-bold">{{ __('User Management') }}</h3>
                    <p class="mt-2">{{ __('Manage all user accounts in the system.') }}</p>
                </div>
                <div class="p-6">
                    <x-soft-delete-filter />

                    {{-- Filter form --}}
                    <form method="GET" action="{{ route('users.index') }}" id="filter-form" class="mb-6">
                        <div class="flex items-end gap-4 flex-wrap">
                            <div class="flex-1 min-w-[200px]">
                                <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Role') }}
                                </label>
                                <select name="role" id="role" class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" onchange="this.form.submit()">
                                    <option value="">{{ __('All Roles') }}</option>
                                    <option value="STUDENT" {{ $roleFilter == 'STUDENT' ? 'selected' : '' }}>{{ __('Student') }}</option>
                                    <option value="TEACHER" {{ $roleFilter == 'TEACHER' ? 'selected' : '' }}>{{ __('Teacher') }}</option>
                                    <option value="VP" {{ $roleFilter == 'VP' ? 'selected' : '' }}>{{ __('VP') }}</option>
                                    <option value="ADMIN" {{ $roleFilter == 'ADMIN' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                                </select>
                            </div>

                            <div id="class-filter-container" class="flex-1 min-w-[200px] {{ $roleFilter != 'STUDENT' ? 'hidden' : '' }}">
                                <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Class') }}
                                </label>
                                <select name="class_id" id="class_id" class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" onchange="this.form.submit()">
                                    <option value="">{{ __('All Classes') }}</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ $classFilter == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if($roleFilter || $classFilter)
                                <div class="flex items-end">
                                    <a href="{{ route('users.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">
                                        {{ __('Clear Filters') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </form>

                    <div class="flex justify-between items-center mb-6">
                        <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ __('Users List') }}
                            ({{ $users->total() }})</h4>
                        @if(!$showDeleted)
                            <a href="{{ route('users.create') }}"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                {{ __('Add User') }}
                            </a>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-800">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('ID') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Name') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Email') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Identifier') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Role') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($users as $user)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ $user->deleted_at ? 'bg-red-50 dark:bg-red-900' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $user->identifier }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($user->role === 'ADMIN') bg-red-100 text-red-800
                                                @elseif($user->role === 'TEACHER') bg-blue-100 text-blue-800
                                                @elseif($user->role === 'VP') bg-green-100 text-green-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $user->role }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($user->deleted_at)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ __('DELETED') }}</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ __('ACTIVE') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            @if($user->deleted_at)
                                                {{-- Restore button --}}
                                                <form action="{{ route('users.restore', $user) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg shadow-md transition-colors" title="{{ __('Restore this user') }}">
                                                        <i class="bi bi-arrow-counterclockwise"></i> {{ __('Restore') }}
                                                    </button>
                                                </form>
                                                {{-- Permanent delete button --}}
                                                <form action="{{ route('users.force-destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to permanently delete this user? This action cannot be undone.') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white px-3 py-1 rounded-lg shadow-md transition-colors" title="{{ __('Permanently delete this user') }}">
                                                        <i class="bi bi-trash"></i> {{ __('Delete Permanently') }}
                                                    </button>
                                                </form>
                                            @else
                                                <button onclick="window.location.href='{{ route('users.edit', $user) }}'" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded-lg shadow-md transition-colors bi bi-pencil-square" title="{{ __('Edit this user') }}"></button>
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg shadow-md transition-colors bi bi-trash" title="{{ __('Soft delete this user') }}" onclick="return confirm('{{ __('Are you sure?') }}');"></button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex justify-between items-center">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Showing :first to :last of :total results', ['first' => $users->firstItem(), 'last' => $users->lastItem(), 'total' => $users->total()]) }}
                        </div>
                        <div class="flex gap-2">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const classFilterContainer = document.getElementById('class-filter-container');

            function toggleClassFilter() {
                if (roleSelect.value === 'STUDENT') {
                    classFilterContainer.classList.remove('hidden');
                } else {
                    classFilterContainer.classList.add('hidden');
                }
            }

            roleSelect.addEventListener('change', toggleClassFilter);
            toggleClassFilter();
        });
    </script>
</x-app-layout>