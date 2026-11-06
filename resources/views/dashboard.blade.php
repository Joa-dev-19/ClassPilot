<x-app-layout>
    <div class="min-h-screen bg-slate-50 px-5 py-8 sm:px-8">
        <main class="mx-auto max-w-7xl">
            <header class="mb-8 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-blue-700">Mon espace ClassPilot</p>
                    <h1 class="mt-1 text-2xl font-bold text-slate-950 sm:text-3xl">
                        Bonjour, {{ auth()->user()->name }} !
                    </h1>
                    <p class="mt-2 text-slate-600">
                        {{ now()->translatedFormat('l d F Y') }}
                    </p>
                </div>
            </header>

            @if (auth()->user()->role === \App\Models\User::ROLE_STUDENT)
                <section aria-labelledby="actions-etudiant">
                    <h2 id="actions-etudiant" class="text-lg font-semibold text-slate-900">
                        Organise ton travail
                    </h2>
                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <a href="{{ route('taches.index') }}"
                           class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-blue-300 hover:shadow focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <h3 class="font-semibold text-slate-900">Mes tâches</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                Consulte et organise tes devoirs, projets et révisions.
                            </p>
                        </a>
                        <a href="{{ route('calendrier.index') }}"
                           class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-blue-300 hover:shadow focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <h3 class="font-semibold text-slate-900">Calendrier et rappels</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                Visualise tes échéances et programme tes rappels.
                            </p>
                        </a>
                    </div>
                </section>
            @elseif (auth()->user()->role === \App\Models\User::ROLE_TEACHER)
                <section aria-labelledby="actions-enseignant">
                    <h2 id="actions-enseignant" class="text-lg font-semibold text-slate-900">
                        Espace enseignant
                    </h2>
                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <a href="{{ route('devoirs.index') }}"
                           class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-blue-300 hover:shadow focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <h3 class="font-semibold text-slate-900">Mes devoirs</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                Crée des devoirs, publie-les pour les étudiants et suis leurs échéances.
                            </p>
                        </a>
                    </div>
                </section>
            @else
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
                         aria-labelledby="espace-personnel">
                    <h2 id="espace-personnel" class="text-lg font-semibold text-slate-900">
                        Bienvenue sur ClassPilot
                    </h2>
                    <p class="mt-2 text-slate-600">
                        Ton compte est connecté. Les outils accessibles dépendent de ton profil.
                    </p>
                </section>
            @endif
        </main>
    </div>
</x-app-layout>
