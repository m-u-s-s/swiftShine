<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-900 text-white">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-gray-200 flex flex-col">
            <div class="p-4 text-xl font-bold">🚀 swiftShine</div>
            <nav class="flex-1 px-2 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded hover:bg-gray-700">📊 Dashboard</a>
                <a href="{{ route('employe.rdv.liste') }}" class="block px-4 py-2 rounded hover:bg-gray-700">📋 RDV</a>
                <a href="{{ route('client.calendrier') }}" class="block px-4 py-2 rounded hover:bg-gray-700">📅 Calendrier</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 p-6 overflow-auto">
            {{ $slot }}
        </div>
    </div>
    
    @stack('scripts')
    @livewireScripts
</body>
</html>
