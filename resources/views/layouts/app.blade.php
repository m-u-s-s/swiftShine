<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css' rel='stylesheet' />
</head>

<body class="font-sans antialiased">


    <div class="min-h-screen bg-gray-100">
        @livewire('navigation-menu')
        @livewire('notifications')

        <!-- Page Heading -->
        @if (isset($header))
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>

        {{-- ✅ Affiche le toast global --}}
        <x-toast />

        <!-- Dashboard résumé si connecté -->
        @auth
        <div class="bg-white mt-6 border-t border-gray-200 shadow-inner py-4 px-6 text-sm text-gray-700">
            <div class="flex flex-col gap-2">
                <p>Connecté en tant que <strong>{{ auth()->user()->role }}</strong></p>

                @if(auth()->user()->role === 'client')
                <a href="{{ route('client.calendrier') }}" class="text-blue-600 underline">📅 Prendre rendez-vous</a>
                <a href="{{ route('client.rdv.formulaire') }}" class="text-blue-600 underline">📋 Formulaire de RDV</a>
                @elseif(auth()->user()->role === 'employe')
                <a href="{{ route('employe.calendrier') }}" class="text-green-600 underline">🗓️ Voir mon planning</a>
                <a href="{{ route('employe.disponibilites') }}" class="text-green-600 underline">📌 Gérer mes disponibilités</a>
                <a href="{{ route('employe.rdv.liste') }}" class="text-green-600 underline">📋 Gérer les RDV</a>
                @endif
            </div>
        </div>
        @endauth
    </div>

    @livewireScripts
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.js'></script>
</body>

</html>