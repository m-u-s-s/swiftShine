<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-mark class="block h-9 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @auth
                        @if (auth()->user()->role === 'client')
                            <x-nav-link href="{{ route('client.calendrier') }}" :active="request()->routeIs('client.calendrier')">
                                Prendre rendez-vous
                            </x-nav-link>
                        @elseif (auth()->user()->role === 'employe')
                            <x-nav-link href="{{ route('employe.calendrier') }}" :active="request()->routeIs('employe.calendrier')">
                                Mon planning
                            </x-nav-link>
                            <x-nav-link href="{{ route('employe.disponibilites') }}" :active="request()->routeIs('employe.disponibilites')">
                                Mes disponibilités
                            </x-nav-link>
                            <x-nav-link href="{{ route('employe.rdv.liste') }}" :active="request()->routeIs('employe.rdv.liste')">
                                RDV en attente
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Right Side -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <!-- Team Management Dropdown (Jetstream) -->
                    @livewire('navigation-menu-team-dropdown')
                @endif

                <!-- Settings Dropdown -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <x-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Profil') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Déconnexion') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive menu (mobile) -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>

            @auth
                @if (auth()->user()->role === 'client')
                    <x-responsive-nav-link href="{{ route('client.calendrier') }}">
                        Prendre rendez-vous
                    </x-responsive-nav-link>
                @elseif (auth()->user()->role === 'employe')
                    <x-responsive-nav-link href="{{ route('employe.calendrier') }}">
                        Mon planning
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('employe.disponibilites') }}">
                        Mes disponibilités
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('employe.rdv.liste') }}">
                        RDV en attente
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>
    </div>
</nav>
