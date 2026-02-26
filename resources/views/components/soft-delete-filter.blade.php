<!-- Soft Delete Filter Component -->
<div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg">
    <form method="GET" action="{{ request()->url() }}" class="flex items-center gap-4">
        <label for="show_deleted_checkbox" class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" id="show_deleted_checkbox" name="show_deleted" value="1" {{ request('show_deleted') ? 'checked' : '' }}
                class="w-4 h-4 text-yellow-600 bg-white border-yellow-300 rounded focus:ring-yellow-500 dark:focus:ring-yellow-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-yellow-600">
            <span class="text-sm font-medium text-yellow-800 dark:text-yellow-300">
                Show Deleted Items
            </span>
        </label>
        <button type="submit"
            class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-md transition-colors">
            Apply Filter
        </button>
        @if(request('show_deleted'))
            <a href="{{ request()->url() }}"
                class="px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white text-sm font-medium rounded-md transition-colors">
                Clear Filter
            </a>
        @endif
    </form>
</div>