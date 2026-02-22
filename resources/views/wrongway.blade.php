<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Wrong Way') }}
        </h2>
    </x-slot>
    <audio autoplay loop class="hidden" id="plaudite">
        <source src="{{ asset('plaudite_interlude.mp3') }}" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
    <script>
        let audio = document.getElementById('plaudite');
        audio.volume = 0.1;
    </script>
    <div class="py-12">
        <div class="max-w-7xl min-h-screen mx-auto sm:px-6 lg:px-8">

            <div class="relative bg-gray-800 rounded-xl shadow-xl">

            <!-- Left Lock -->
            <img src="{{ asset('img/storylock/lock_side_closed.png') }}"
                 class="absolute top-1/2 left-[25%] -translate-x-1/2 -translate-y-1/2 w-40 md:w-52"
                 alt="Left Lock">

            <!-- Center Lock -->
            <img src="{{ asset('img/storylock/lock_main_closed.png') }}"
                 class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-48 md:w-64"
                 alt="Center Lock">

            <!-- Right Lock -->
            <img src="{{ asset('img/storylock/lock_side_closed.png') }}"
                 class="absolute top-1/2 left-[75%] -translate-x-1/2 -translate-y-1/2 w-40 md:w-52"
                 alt="Right Lock">

            </div>
        </div>
    </div>  
</x-app-layout>