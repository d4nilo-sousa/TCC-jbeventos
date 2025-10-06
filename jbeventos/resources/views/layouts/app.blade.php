<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Exemplo com a fonte Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&display=swap" rel="stylesheet">


    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        window.Laravel = {
            userId: @json(auth()->id())
        };
    </script>

    @livewireStyles
</head>

<body class="font-sans antialiased h-screen flex flex-col">
    <x-banner />
    @livewire('navigation-menu')

    @props(['backgroundClass' => 'bg-gradient-to-br from-red-400 via-orange-100 to-red-100'])

    <div class="flex-1 bg-gray-100 pt-[60px] lg:pt-[70px]">
        @if (isset($header))
            {{-- Removido o padding da navbar do header, pois o pt já está no div pai --}}
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main class="flex-1">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
                {{ $slot }}
            </div>
        </main>
</div>

    @stack('modals')
    @livewireScripts
</body>

</html>
