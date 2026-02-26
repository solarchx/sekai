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
                    New Announcement
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-soft-delete-filter />
            <div class="space-y-4">
                @forelse($announcements as $announcement)
                    <div
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 {{ $announcement->deleted_at ? 'border-2 border-red-500 opacity-75' : '' }}">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h3
                                    class="text-xl font-bold text-gray-900 dark:text-gray-100 {{ $announcement->deleted_at ? 'line-through' : '' }}">
                                    {{ $announcement->title }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $announcement->subtitle }}</p>
                            </div>
                            <div class="flex gap-2 items-center">
                                @if($announcement->deleted_at)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">DELETED</span>
                                @endif
                                @if(auth()->user()->role !== 'STUDENT' && (auth()->id() === $announcement->sender_id || auth()->user()->role === 'ADMIN'))
                                    <div class="flex gap-2">
                                        @if($announcement->deleted_at)
                                            <form action="{{ route('announcements.restore', $announcement) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm"
                                                    title="Restore">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('announcements.edit', $announcement) }}"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">Edit</a>
                                            <form action="{{ route('announcements.destroy', $announcement) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm"
                                                    onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <p class="text-gray-800 dark:text-gray-200 mb-4">{{ $announcement->content }}</p>

                        <div class="text-xs text-gray-500 dark:text-gray-400 text-right">
                            <p>From: <strong>{{ $announcement->sender->name }}</strong></p>
                            <p>Scope: <strong>{{ $announcement->scope }}</strong>
                                @if($announcement->activity_id)
                                    - {{ $announcement->activity->subject->name }}
                                @elseif($announcement->grade_id)
                                    - Grade {{ $announcement->grade->id }}
                                @endif
                            </p>
                        </div>
                    </div>
                @empty
                    <div
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 text-center text-gray-500">
                        No announcements yet.
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class=\"mt-6 flex justify-between items-center\">
                <div class=\"text-sm text-gray-600 dark:text-gray-400\">
                    Showing {{ $announcements->firstItem() }} to {{ $announcements->lastItem() }} of
                    {{ $announcements->total() }} results
                </div>
                <div class=\"flex gap-2\">
                    {{ $announcements->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>