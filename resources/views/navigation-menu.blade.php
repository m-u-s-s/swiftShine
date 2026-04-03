<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-xl font-extrabold text-blue-700">
                        {{ config('app.name', 'App') }}
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        @if(auth()->user()->role === 'client')
                            <x-nav-link :href="route('client.dashboard')" :active="request()->routeIs('client.dashboard')">Accueil</x-nav-link>
                            <x-nav-link :href="route('client.rendezvous.create')" :active="request()->routeIs('client.rendezvous.create')">Nouveau rendez-vous</x-nav-link>
                            <x-nav-link :href="route('client.rendezvous.index')" :active="request()->routeIs('client.rendezvous.index')">Mes rendez-vous</x-nav-link>
                            <x-nav-link :href="route('client.historique')" :active="request()->routeIs('client.historique')">Historique</x-nav-link>
                            <x-nav-link :href="route('client.profile')" :active="request()->routeIs('client.profile')">Profil</x-nav-link>
                        @elseif(auth()->user()->role === 'employe')
                            <x-nav-link :href="route('employe.dashboard')" :active="request()->routeIs('employe.dashboard')">Ma journée</x-nav-link>
                            <x-nav-link :href="route('employe.missions')" :active="request()->routeIs('employe.missions')">Mes missions</x-nav-link>
                            <x-nav-link :href="route('employe.historique')" :active="request()->routeIs('employe.historique')">Historique</x-nav-link>
                        @elseif(auth()->user()->role === 'admin')
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Pilotage</x-nav-link>
                            <x-nav-link :href="route('admin.planning')" :active="request()->routeIs('admin.planning')">Planning</x-nav-link>
                            <x-nav-link :href="route('admin.missions')" :active="request()->routeIs('admin.missions')">Missions</x-nav-link>
                            <x-nav-link :href="route('admin.utilisateurs')" :active="request()->routeIs('admin.utilisateurs')">Utilisateurs</x-nav-link>
                            <x-nav-link :href="route('admin.feedbacks')" :active="request()->routeIs('admin.feedbacks')">Feedbacks</x-nav-link>
                            <x-nav-link :href="route('admin.outils')" :active="request()->routeIs('admin.outils')">Outils</x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                @auth
                    @if(auth()->user()->role === 'client')
                        <a href="{{ route('client.rendezvous.create') }}"
                           class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">
                            Réserver
                        </a>
                    @endif

                    <div class="relative ms-3">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none transition ease-in-out duration-150">
                                    <div>{{ Auth::user()->name }}</div>
                                    <div class="ms-2">
                                        <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Compte
                                </div>

                                @if(auth()->user()->role === 'client')
                                    <x-dropdown-link :href="route('client.profile')">Profil</x-dropdown-link>
                                @else
                                    <x-dropdown-link :href="route('profile.show')">Profil</x-dropdown-link>
                                @endif

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault(); this.closest('form').submit();">
                                        Déconnexion
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-blue-700">Connexion</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">Inscription</a>
                @endauth
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-100 bg-white">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                @if(auth()->user()->role === 'client')
                    <x-responsive-nav-link :href="route('client.dashboard')" :active="request()->routeIs('client.dashboard')">Accueil</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('client.rendezvous.create')" :active="request()->routeIs('client.rendezvous.create')">Nouveau rendez-vous</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('client.rendezvous.index')" :active="request()->routeIs('client.rendezvous.index')">Mes rendez-vous</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('client.historique')" :active="request()->routeIs('client.historique')">Historique</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('client.profile')" :active="request()->routeIs('client.profile')">Profil</x-responsive-nav-link>
                @elseif(auth()->user()->role === 'employe')
                    <x-responsive-nav-link :href="route('employe.dashboard')" :active="request()->routeIs('employe.dashboard')">Ma journée</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('employe.missions')" :active="request()->routeIs('employe.missions')">Mes missions</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('employe.historique')" :active="request()->routeIs('employe.historique')">Historique</x-responsive-nav-link>
                @elseif(auth()->user()->role === 'admin')
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Pilotage</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.planning')" :active="request()->routeIs('admin.planning')">Planning</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.missions')" :active="request()->routeIs('admin.missions')">Missions</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.utilisateurs')" :active="request()->routeIs('admin.utilisateurs')">Utilisateurs</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.feedbacks')" :active="request()->routeIs('admin.feedbacks')">Feedbacks</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.outils')" :active="request()->routeIs('admin.outils')">Outils</x-responsive-nav-link>
                @endif
            @endauth
        </div>

        @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    @if(auth()->user()->role === 'client')
                        <x-responsive-nav-link :href="route('client.profile')" :active="request()->routeIs('client.profile')">Profil</x-responsive-nav-link>
                    @else
                        <x-responsive-nav-link :href="route('profile.show')" :active="request()->routeIs('profile.show')">Profil</x-responsive-nav-link>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            Déconnexion
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-4 border-t border-gray-200 space-y-1">
                <x-responsive-nav-link :href="route('login')" :active="request()->routeIs('login')">Connexion</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')" :active="request()->routeIs('register')">Inscription</x-responsive-nav-link>
            </div>
        @endauth
    </div>
</nav>