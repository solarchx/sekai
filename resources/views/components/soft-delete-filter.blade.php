<div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg">
    <div class="flex items-center gap-4">
        <a href="{{ request()->fullUrlWithQuery(['show_deleted' => request('show_deleted') ? null : 1]) }}" 
           class="flex items-center gap-2 cursor-pointer no-underline text-yellow-800 dark:text-yellow-300">
            <span class="w-4 h-4 inline-block border-2 rounded {{ request('show_deleted') ? 'bg-yellow-600 border-yellow-600' : 'bg-white border-yellow-300 dark:bg-gray-700 dark:border-yellow-600' }}"></span>
            <span class="text-sm font-medium">
                Show Deleted Items
            </span>
        </a>
        @if(request('show_deleted'))
            <a href="{{ request()->fullUrlWithQuery(['show_deleted' => null]) }}"
                class="px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white text-sm font-medium rounded-md transition-colors">
                Clear Filter
            </a>
        @endif
    </div>
</div>