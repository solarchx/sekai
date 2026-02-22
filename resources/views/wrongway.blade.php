<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Wrong Way') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="relative bg-gray-800 h-96 rounded-lg overflow-hidden">

    <!-- Left Lock -->
    <img src="{{ asset('img/storylock/lock_side_closed.png') }}"
         class="absolute top-1/2 left-1/4 -translate-x-1/2 -translate-y-1/2 w-32 md:w-48"
         alt="Left Lock">

    <!-- Center Lock -->
    <img src="{{ asset('img/storylock/lock_main_closed.png') }}"
         class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-40 md:w-56"
         alt="Center Lock">

    <!-- Right Lock -->
    <img src="{{ asset('img/storylock/lock_side_closed.png') }}"
         class="absolute top-1/2 left-3/4 -translate-x-1/2 -translate-y-1/2 w-32 md:w-48"
         alt="Right Lock">
        </div>
    </div>
</x-app-layout>