<div class="mb-4 flex items-center gap-3">
    <label for="show_deleted" class="inline-flex items-center gap-2 cursor-pointer">
        <input 
            type="checkbox" 
            id="show_deleted" 
            name="show_deleted" 
            value="1"
            {{ request('show_deleted') ? 'checked' : '' }}
            onchange="this.form.submit()"
            class="w-4 h-4 border-gray-300 rounded"
        >
        <span class="text-sm text-gray-700 dark:text-gray-300">Show Deleted Records</span>
    </label>
    <form id="deleteFilter" method="GET" class="hidden">
        <input type="hidden" name="show_deleted" value="{{ request('show_deleted') }}">
    </form>
</div>
