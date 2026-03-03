<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Grade Management') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6" style="background: linear-gradient(to right, #ec4899, #f43f5e); color: white;">
                    <h3 class="text-2xl font-bold">{{ __('Grade Management') }}</h3>
                    <p class="mt-2">{{ __('Manage grade levels.') }}</p>
                </div>
                <div class="p-6">
                    <x-soft-delete-filter />
                    <div class="flex justify-between items-center mb-6">
                        <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ __('Grades List') }}
                            ({{ $grades->total() }})</h4>
                        @if(!$showDeleted)
                            <a href="{{ route('grades.create') }}"
                                class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                {{ __('Add Grade') }}
                            </a>
                        @endif
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-800">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('ID') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Status') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($grades as $grade)
                                    <tr
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ $grade->deleted_at ? 'bg-red-50 dark:bg-red-900' : '' }}">
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $grade->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($grade->deleted_at)
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ __('DELETED') }}</span>
                                            @else
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ __('ACTIVE') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            @if($grade->deleted_at)
                                                <form action="{{ route('grades.restore', $grade) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors"
                                                        title="Restore">
                                                        <i class="bi bi-arrow-counterclockwise"></i> {{ __('Restore') }}
                                                    </button>
                                                </form>
                                            @else
                                                <button onclick="window.location.href='{{ route('grades.edit', $grade) }}'"
                                                    class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors"
                                                    title="Edit">{{ __('Edit') }}</button>
                                                <form action="{{ route('grades.destroy', $grade) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors"
                                                        title="Delete"
                                                        onclick="return confirm('{{ __('Are you sure?') }}')">{{ __('Delete') }}</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            {{ __('No grades found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>


                    <div class="mt-6 flex justify-between items-center">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Showing {{ $grades->firstItem() }} to {{ $grades->lastItem() }} of {{ $grades->total() }}
                            results
                        </div>
                        <div class="flex gap-2">
                            {{ $grades->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>