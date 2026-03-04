@props(['extraClasses' => ''])

<x-dropdown align="left" width="32">
    <x-slot name="trigger">
        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none transition ease-in-out duration-150 {{ $extraClasses }}">
            <i class="bi bi-globe text-lg"></i>
        </button>
    </x-slot>

    <x-slot name="content">
        <a href="{{ route('language.switch', 'en') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ app()->getLocale() === 'en' ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
            <i class="bi bi-check-lg {{ app()->getLocale() === 'en' ? 'text-green-500' : 'text-transparent' }}"></i>
            English
        </a>
        <a href="{{ route('language.switch', 'id') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ app()->getLocale() === 'id' ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
            <i class="bi bi-check-lg {{ app()->getLocale() === 'id' ? 'text-green-500' : 'text-transparent' }}"></i>
            Bahasa Indonesia
        </a>
    </x-slot>
</x-dropdown>
