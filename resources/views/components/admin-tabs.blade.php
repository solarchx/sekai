@props(['active' => ''])

<nav class="flex items-center space-x-2 bg-white dark:bg-gray-800 p-2 rounded-lg shadow-sm">
    <a href="{{ route('users.index') }}" class="px-3 py-2 rounded-md text-sm font-medium transition 
        {{ $active === 'users' ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
        Users
    </a>

    <a href="{{ route('classes.index') }}" class="px-3 py-2 rounded-md text-sm font-medium transition 
        {{ $active === 'classes' ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
        Classes
    </a>

    <a href="{{ route('majors.index') }}" class="px-3 py-2 rounded-md text-sm font-medium transition 
        {{ $active === 'majors' ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
        Majors
    </a>
</nav>
