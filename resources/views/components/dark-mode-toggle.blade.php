@props(['extraClasses' => ''])

<button 
    x-data="{ 
        dark: localStorage.getItem('dark-mode') === 'true' || (localStorage.getItem('dark-mode') === null && window.matchMedia('(prefers-color-scheme: dark)').matches)
    }"
    @click="
        dark = !dark; 
        localStorage.setItem('dark-mode', dark);
        if (dark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    "
    :class="{ 'bg-gray-200 dark:bg-gray-700': true }"
    class="p-2 rounded-lg transition-colors duration-200 text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 {{ $extraClasses }}"
    type="button"
    title="Toggle dark mode"
>
    <!-- Sun Icon (shown in dark mode) -->
    <i x-show="dark" class="bi bi-sun text-lg"></i>
    
    <!-- Moon Icon (shown in light mode) -->
    <i x-show="!dark" class="bi bi-moon text-lg"></i>
</button>
