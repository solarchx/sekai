<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Announcements') }}
            </h2>
            @if(auth()->user()->role !== 'STUDENT')
                <a href="{{ route('announcements.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ __('New Announcement') }}
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-4">
                @forelse($announcements as $announcement)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $announcement->title }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $announcement->subtitle }}</p>
                            </div>
                            @if(auth()->user()->role !== 'STUDENT' && (auth()->id() === $announcement->sender_id || auth()->user()->role === 'ADMIN'))
                                <div class="flex gap-2">
                                    <a href="{{ route('announcements.edit', $announcement) }}"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">Edit</a>
                                    <form action="{{ route('announcements.destroy', $announcement) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm"
                                            onclick="return confirm('{{ __('Are you sure?') }}')">{{ __('Delete') }}</button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        <p class="text-gray-800 dark:text-gray-200 mb-4">{{ $announcement->content }}</p>

                        <div class="text-xs text-gray-500 dark:text-gray-400 text-right">
                            <p>From: <strong>{{ $announcement->sender->name }}</strong></p>
                            <p>Scope: <strong>{{ $announcement->scope }}</strong>
                                @if($announcement->activity_id)
                                    – {{ $announcement->activity->subject->name }} ({{ $announcement->activity->class->name }})
                                @elseif($announcement->grade_id)
                                    – Grade {{ $announcement->grade->id }}
                                @endif
                            </p>
                        </div>
                    </div>
                @empty
                    <div
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 text-center text-gray-500">
                        {{ __('No announcements yet.') }}
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $announcements->links() }}
            </div>
        </div>
    </div>
</x-app-layout>