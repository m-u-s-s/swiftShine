<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SwiftShine') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-800">

    <x-banner />

    <div class="min-h-screen bg-gray-100 pb-20 sm:pb-0">
        @livewire('navigation-menu')

        <!-- Page Heading -->
        @if (isset($header))
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif

        <!-- Page Content -->
        <main class="py-6 px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>
    </div>

    <!-- ✅ Toast réutilisable -->
    <x-toast />

    <!-- ✅ Sons pour notifications -->
    <audio id="success-sound" src="{{ asset('sounds/success.mp3') }}" preload="auto"></audio>
    <audio id="error-sound" src="{{ asset('sounds/error.mp3') }}" preload="auto"></audio>

    <!-- ✅ Notifications Jetstream Livewire -->
    @if (class_exists(\App\Livewire\Notifications::class))
    @livewire('notifications')
    @endif

    @stack('modals')
    @livewireScripts
    @stack('scripts')
    @auth
    @if(auth()->user()->role === 'client')
    <div class="sm:hidden fixed bottom-0 inset-x-0 z-50 border-t bg-white/95 backdrop-blur shadow-lg">
        <div class="grid grid-cols-4 h-16">
            <a href="{{ route('client.dashboard') }}"
                class="flex flex-col items-center justify-center text-xs {{ request()->routeIs('client.dashboard') ? 'text-blue-600 font-semibold' : 'text-gray-500' }}">
                <span>🏠</span>
                <span>Accueil</span>
            </a>

            <a href="{{ route('client.rendezvous.create') }}"
                class="flex flex-col items-center justify-center text-xs {{ request()->routeIs('client.rendezvous.create') ? 'text-blue-600 font-semibold' : 'text-gray-500' }}">
                <span>➕</span>
                <span>Réserver</span>
            </a>

            <a href="{{ route('client.rendezvous.index') }}"
                class="flex flex-col items-center justify-center text-xs {{ request()->routeIs('client.rendezvous.index') ? 'text-blue-600 font-semibold' : 'text-gray-500' }}">
                <span>📅</span>
                <span>Rendez-vous</span>
            </a>

            <a href="{{ route('client.historique') }}"
                class="flex flex-col items-center justify-center text-xs {{ request()->routeIs('client.historique') ? 'text-blue-600 font-semibold' : 'text-gray-500' }}">
                <span>🕘</span>
                <span>Historique</span>
            </a>
        </div>
    </div>
    @endif
    @endauth
</body>

</html>