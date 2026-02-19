<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 transition-colors duration-200">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 active:text-gray-900 dark:active:text-white">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 active:text-gray-900 dark:active:text-white">
                        {{ __('Profile') }}
                    </x-nav-link>
                </div>
                    {{-- Admin Specific Links --}}
                    @if(auth()->user()->role === 'ADMIN')
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 active:text-gray-900 dark:active:text-white">
                            {{ __('Users') }}
                        </x-nav-link>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('majors.index')" :active="request()->routeIs('majors.*')" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 active:text-gray-900 dark:active:text-white">
                            {{ __('Majors') }}
                        </x-nav-link>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('classes.index')" :active="request()->routeIs('classes.*')" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 active:text-gray-900 dark:active:text-white">
                            {{ __('Classes') }}
                        </x-nav-link>
                    </div>
                    @endif
                    {{-- Student Specific Links --}}
                    @if (auth()->user()->role === 'STUDENT' || auth()->user()->role === 'TEACHER')
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('class.show')" :active="request()->routeIs('student.classes.show')" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 active:text-gray-900 dark:active:text-white">
                            {{ __('My Class') }}
                        </x-nav-link>
                    </div>
                    @endif
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Dark Mode Toggle -->
                <div class="me-4">
                    <x-dark-mode-toggle />
                </div>
                
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <i class="bi bi-chevron-down text-sm"></i>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <x-dark-mode-toggle class="me-2" />
                
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <i :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex bi bi-list text-xl"></i>
                    <i :class="{'hidden': ! open, 'inline-flex': open }" class="hidden bi bi-x text-xl"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-700">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
