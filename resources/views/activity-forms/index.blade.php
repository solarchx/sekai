<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Activity Form Management') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6" style="background: linear-gradient(to right, #8b5cf6, #a855f7); color: white;">
                    <h3 class="text-2xl font-bold">{{ __('Activity Form Management') }}</h3>
                    <p class="mt-2">{{ __('Select an activity to view its forms.') }}</p>
                </div>
                <div class="p-6">
                    <x-soft-delete-filter />

                    <form method="GET" action="{{ route('activity-forms.index') }}" id="activity-form">
                        <div class="mb-6 flex items-end gap-4">
                            <div class="flex-1">
                                <label for="activity_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Activity') }}
                                </label>
                                <select name="activity_id" id="activity_id"
                                    class="block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                    onchange="this.form.submit()">
                                    <option value="">{{ __('-- All Activities --') }}</option>
                                    @foreach($activities as $activity)
                                        <option value="{{ $activity->id }}" {{ $activityId == $activity->id ? 'selected' : '' }}>
                                            {{ $activity->subject->name }} – {{ $activity->class->name }}
                                            ({{ $activity->period->weekday_name }}
                                            {{ $activity->period->time_begin }}-{{ $activity->period->time_end }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if(!$showDeleted)
                                <div>
                                    <a href="{{ route('activity-forms.create') }}"
                                        class="bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors inline-block">
                                        {{ __('Add Form') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </form>

                    <div class="flex justify-between items-center mb-6">
                        <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ __('Forms List') }} ({{ $forms->total() }})
                        </h4>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-800">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Activity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($forms as $form)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ $form->deleted_at ? 'bg-red-50 dark:bg-red-900' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $form->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $form->activity->subject->name ?? __('N/A') }} -
                                            {{ $form->activity->class->name ?? __('N/A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $form->activity_date }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($form->deleted_at)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ __('DELETED') }}</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ __('ACTIVE') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            @if($form->deleted_at)
                                                {{-- Restore button --}}
                                                <form action="{{ route('activity-forms.restore', $form) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg shadow-md transition-colors" title="Restore">
                                                        <i class="bi bi-arrow-counterclockwise"></i> {{ __('Restore') }}
                                                    </button>
                                                </form>
                                                {{-- Permanent delete button --}}
                                                <form action="{{ route('activity-forms.force-destroy', $form) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to permanently delete this form? All associated presences and reports will also be deleted. This action cannot be undone.') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white px-3 py-1 rounded-lg shadow-md transition-colors" title="Permanently delete this form">
                                                        <i class="bi bi-trash"></i> {{ __('Delete Permanently') }}
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('activity-forms.show', $form) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-xs inline-block">{{ __('View') }}</a>
                                                <a href="{{ route('activity-forms.edit', $form) }}" class="bg-violet-600 hover:bg-violet-700 text-white px-3 py-1 rounded-lg text-xs inline-block">{{ __('Edit') }}</a>
                                                <form action="{{ route('activity-forms.destroy', $form) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-xs">{{ __('Delete') }}</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">{{ __('No forms found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex justify-between items-center">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Showing') }} {{ $forms->firstItem() }} {{ __('to') }} {{ $forms->lastItem() }} {{ __('of') }} {{ $forms->total() }} {{ __('results') }}
                        </div>
                        <div class="flex gap-2">
                            {{ $forms->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>