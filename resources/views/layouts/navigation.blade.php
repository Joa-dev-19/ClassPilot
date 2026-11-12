<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-[4.5rem] items-center justify-between gap-4">
            <div class="flex min-w-0 items-center gap-8">
                <div class="flex shrink-0 items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl focus-visible:ring-2 focus-visible:ring-emerald-600">
                        <x-application-logo class="block h-10 w-10 fill-current text-blue-700" />
                        <span class="hidden text-lg font-bold tracking-tight text-slate-900 sm:inline">ClassPilot</span>
                    </a>
                </div>

                <div class="hidden items-center gap-1 lg:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Tableau de bord
                    </x-nav-link>
                    @if (Auth::user()->role === \App\Models\User::ROLE_STUDENT)
                        <x-nav-link :href="route('taches.index')" :active="request()->routeIs('taches.*')">
                            Mes tâches
                        </x-nav-link>
                        <x-nav-link :href="route('devoirs.etudiants.index')" :active="request()->routeIs('devoirs.etudiants.*')">
                            Devoirs
                        </x-nav-link>
                        <x-nav-link :href="route('calendrier.index')" :active="request()->routeIs('calendrier.*')">
                            Calendrier
                        </x-nav-link>
                    @elseif (Auth::user()->role === \App\Models\User::ROLE_TEACHER)
                        <x-nav-link :href="route('devoirs.index')" :active="request()->routeIs('devoirs.*')">
                            Mes devoirs
                        </x-nav-link>
                    @elseif (Auth::user()->role === \App\Models\User::ROLE_ADMINISTRATOR)
                        <x-nav-link :href="route('admin.utilisateurs.index')" :active="request()->routeIs('admin.utilisateurs.*')">
                            Comptes
                        </x-nav-link>
                        <x-nav-link :href="route('admin.filieres.index')" :active="request()->routeIs('admin.filieres.*', 'admin.classes.*')">
                            Classes et filières
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden items-center gap-3 lg:flex">
                <span class="hidden rounded-full bg-sand-100 px-3 py-1 text-xs font-semibold text-sand-800 xl:inline">
                    {{ match (Auth::user()->role) {
                        \App\Models\User::ROLE_STUDENT => 'Espace étudiant',
                        \App\Models\User::ROLE_TEACHER => 'Espace enseignant',
                        default => 'Administration',
                    } }}
                </span>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex max-w-56 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 focus-visible:ring-2 focus-visible:ring-emerald-600">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-sm font-bold text-emerald-900" aria-hidden="true">
                                {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span class="truncate">{{ Auth::user()->name }}</span>

                            <span class="ms-1 text-slate-400" aria-hidden="true">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Mon profil
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button type="submit" class="block w-full px-4 py-2.5 text-start text-sm font-medium leading-5 text-coral-700 transition hover:bg-coral-50 focus-visible:bg-coral-50">
                                Se déconnecter
                            </button>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="flex items-center lg:hidden">
                <button type="button" @click="open = ! open" :aria-expanded="open.toString()" aria-controls="navigation-mobile" aria-label="Ouvrir ou fermer le menu" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 transition hover:bg-slate-50 focus-visible:ring-2 focus-visible:ring-emerald-600">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div id="navigation-mobile" :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-slate-200/80 bg-white px-4 pb-5 pt-3 lg:hidden">
        <div class="flex flex-col gap-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Tableau de bord
            </x-responsive-nav-link>
            @if (Auth::user()->role === \App\Models\User::ROLE_STUDENT)
                <x-responsive-nav-link :href="route('taches.index')" :active="request()->routeIs('taches.*')">
                    Mes tâches
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('devoirs.etudiants.index')" :active="request()->routeIs('devoirs.etudiants.*')">
                    Devoirs
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('calendrier.index')" :active="request()->routeIs('calendrier.*')">
                    Calendrier
                </x-responsive-nav-link>
            @elseif (Auth::user()->role === \App\Models\User::ROLE_TEACHER)
                <x-responsive-nav-link :href="route('devoirs.index')" :active="request()->routeIs('devoirs.*')">
                    Mes devoirs
                </x-responsive-nav-link>
            @elseif (Auth::user()->role === \App\Models\User::ROLE_ADMINISTRATOR)
                <x-responsive-nav-link :href="route('admin.utilisateurs.index')" :active="request()->routeIs('admin.utilisateurs.*')">
                    Comptes
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.filieres.index')" :active="request()->routeIs('admin.filieres.*', 'admin.classes.*')">
                    Classes et filières
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="mt-4 flex flex-col gap-3 border-t border-slate-200 pt-4">
            <div class="flex items-center gap-3 px-2">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 font-bold text-emerald-900" aria-hidden="true">
                    {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                </span>
                <div class="min-w-0">
                    <div class="truncate text-sm font-semibold text-slate-900">{{ Auth::user()->name }}</div>
                    <div class="truncate text-xs text-slate-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Mon profil
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="block w-full rounded-xl px-4 py-3 text-start text-sm font-medium text-coral-700 transition hover:bg-coral-50 focus-visible:ring-2 focus-visible:ring-coral-600">
                        Se déconnecter
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
