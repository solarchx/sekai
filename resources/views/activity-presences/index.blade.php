<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Activity Presence Management') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6" style="background: linear-gradient(to right, #3b82f6, #1d4ed8); color: white;">
                    <h3 class="text-2xl font-bold">Activity Presence Management</h3>
                    <p class="mt-2">Manage student presence records.</p>
                </div>
                <div class="p-6">
                    @if(auth()->user()->role === 'ADMIN')
                        <div class="mb-4 flex items-center">
                            <form method="GET" action="{{ route('activity-presences.index') }}">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="show_deleted" value="1" {{ request('show_deleted') ? 'checked' : '' }} onchange="this.form.submit()">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Show deleted records</span>
                                </label>
                            </form>
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-6">
                        <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Presences List
                            ({{ $presences->total() }})</h4>
                        @if(!$showDeleted)
                            <a href="{{ route('activity-presences.create') }}"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Presence
                            </a>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-800">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Form</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Location</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @php
                                    $statusLabels = [0 => 'Absent', 1 => 'Permitted Leave', 2 => 'Sick Leave', 3 => 'Present'];
                                    $statusColors = [0 => 'red', 1 => 'yellow', 2 => 'orange', 3 => 'green'];
                                @endphp
                                @forelse($presences as $presence)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ $presence->trashed() ? 'opacity-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $presence->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $presence->student->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">Form #{{ $presence->form_id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="bg-{{ $statusColors[$presence->score] }}-100 dark:bg-{{ $statusColors[$presence->score] }}-900 text-{{ $statusColors[$presence->score] }}-800 dark:text-{{ $statusColors[$presence->score] }}-100 px-3 py-1 rounded-full text-xs font-semibold">
                                            {{ $statusLabels[$presence->score] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $presence->location ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($presence->trashed())
                                            @if(auth()->user()->role === 'ADMIN')
                                                <form action="{{ route('activity-presences.restore', $presence) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">Restore</button>
                                                </form>
                                            @endif
                                        @else
                                            <a href="{{ route('activity-presences.edit', $presence) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors inline-block">Edit</a>
                                            <form action="{{ route('activity-presences.destroy', $presence) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No presences found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6 flex justify-between items-center">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Showing {{ $presences->firstItem() }} to {{ $presences->lastItem() }} of
                            {{ $presences->total() }} results
                        </div>
                        <div class="flex gap-2">
                            {{ $presences->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>