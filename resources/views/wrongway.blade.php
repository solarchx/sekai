<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Wrong Way</title>
</head>
<body class="bg-black overflow-hidden max-w-screen max-h-screen">
    <audio autoplay loop class="hidden" id="plaudite">
        <source src="{{ asset('plaudite_interlude.mp3') }}" type="audio/mpeg">
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
</body>
</html>