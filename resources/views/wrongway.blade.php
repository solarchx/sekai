<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <script>
            // Initialize dark mode on page load
            if (localStorage.getItem('dark-mode') === 'true' || (localStorage.getItem('dark-mode') === null && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        </script>
        <title>Wrong Way - {{ config('app.name', 'Laravel') }}</title>
</head>
<body class="bg-black overflow-hidden max-w-screen max-h-screen">
    <audio autoplay loop class="hidden" id="plaudite">
        <source src="{{ asset('audio/plaudite_interlude.mp3') }}" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
    <script>
        let audio = document.getElementById('plaudite');
        audio.volume = 0.11;
    </script>
    <!-- Left Lock -->
    <img src="{{ asset('img/storylock/lock_side_closed.png') }}"
            class="absolute top-1/4 left-1/4 -translate-x-1/2 -translate-y-1/2 w-4/5 z-1"
            alt="Left Lock I">

    <img src="{{ asset('img/storylock/lock_side_closed.png') }}"
            class="absolute top-3/4 left-1/4 -translate-x-1/2 -translate-y-1/2 w-4/5 z-1"
            alt="Left Lock II">

    <!-- Center Lock -->
    <img src="{{ asset('img/storylock/lock_main_closed.png') }}"
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-4/5 z-2"
            alt="Center Lock I">

    <!-- Right Lock -->
    <img src="{{ asset('img/storylock/lock_side_closed.png') }}"
            class="absolute top-1/4 left-3/4 -translate-x-1/2 -translate-y-1/2 w-4/5 z-1"
            alt="Right Lock I">
    
    <img src="{{ asset('img/storylock/lock_side_closed.png') }}"
            class="absolute top-3/4 left-3/4 -translate-x-1/2 -translate-y-1/2 w-4/5 z-1"
            alt="Right Lock II">
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center text-white z-3">
        <h1 class="text-6xl font-monaco">Wrong Way</h1>
    </div>
    <div class="absolute top-3/4 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center text-white z-3">
        <p class="text-3xl font-monaco">You shouldn't be here. Please go back.</p>
        <a href="{{ route('dashboard') }}" class="font-monaco mt-6 inline-block outline-none text-white px-4 py-2 rounded-lg shadow-md transition-colors">
            Go Back to Dashboard
        </a>
    </div>
</body>
</html>