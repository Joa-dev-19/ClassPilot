<x-app-layout>
    <div class="cp-page">
        <div class="cp-page-inner flex flex-col gap-7">
            <section class="relative overflow-hidden rounded-4xl border border-white/80 bg-gradient-to-br from-emerald-100 via-white to-sand-100 p-6 shadow-card sm:p-9">
                <div class="absolute -right-10 -top-14 h-48 w-48 rounded-full bg-coral-100/70 blur-2xl" aria-hidden="true"></div>
                <div class="absolute bottom-0 right-1/4 h-20 w-20 rounded-full bg-sand-200/80 blur-xl" aria-hidden="true"></div>
                <div class="relative flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                    <div class="flex flex-col gap-3">
                        <span class="cp-eyebrow">
                            {{ match (auth()->user()->role) {
                                \App\Models\User::ROLE_STUDENT => 'Ton espace étudiant',
                                \App\Models\User::ROLE_TEACHER => 'Ton espace enseignant',
                                default => 'Espace administration',
                            } }}
                        </span>
                        <h1 class="cp-heading max-w-2xl text-3xl sm:text-4xl">
                            Bonjour, {{ auth()->user()->name }}.
                        </h1>
                        <p class="max-w-xl text-sm leading-6 text-slate-600 sm:text-base">
                            {{ now()->translatedFormat('l d F Y') }} <span aria-hidden="true">·</span>
                            {{ auth()->user()->role === \App\Models\User::ROLE_STUDENT
                                ? 'Prends le temps de choisir ta prochaine étape.'
                                : 'Retrouve rapidement tes outils de travail.' }}
                        </p>
                    </div>
                    @if (auth()->user()->role === \App\Models\User::ROLE_STUDENT)
                        <a href="{{ route('taches.create') }}" class="cp-button-primary shrink-0">
                            <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4" aria-hidden="true">
                                <path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                            </svg>
                            Ajouter une tâche
                        </a>
                    @elseif (auth()->user()->role === \App\Models\User::ROLE_TEACHER)
                        <a href="{{ route('devoirs.create') }}" class="cp-button-primary shrink-0">
                            <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4" aria-hidden="true">
                                <path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                            </svg>
                            Créer un devoir
                        </a>
                    @endif
                </div>
            </section>

            @if (auth()->user()->role === \App\Models\User::ROLE_STUDENT)
                <section class="flex flex-col gap-4" aria-labelledby="actions-etudiant">
                    <div class="flex flex-col gap-1">
                        <h2 id="actions-etudiant" class="text-xl font-bold tracking-tight text-slate-900">
                            Ton organisation
                        </h2>
                        <p class="text-sm text-slate-600">Les bons outils pour garder le rythme sans te disperser.</p>
                    </div>
                    <div class="grid gap-4 md:grid-cols-3">
                        <a href="{{ route('taches.index') }}"
                           class="group cp-card flex min-h-56 flex-col gap-5 p-6 transition duration-200 hover:-translate-y-1 hover:shadow-lift focus-visible:ring-2 focus-visible:ring-emerald-600">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800 transition group-hover:bg-emerald-200" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6">
                                    <path d="M8 6h12M8 12h12M8 18h12M4 6h.01M4 12h.01M4 18h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                </svg>
                            </span>
                            <span class="flex flex-1 flex-col gap-2">
                                <span class="text-lg font-bold text-slate-900">Mes tâches</span>
                                <span class="text-sm leading-6 text-slate-600">Note tes révisions et travaux, puis suis leur avancement.</span>
                            </span>
                            <span class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-800">
                                Voir mes tâches
                                <span aria-hidden="true" class="transition group-hover:translate-x-1">→</span>
                            </span>
                        </a>

                        <a href="{{ route('devoirs.etudiants.index') }}"
                           class="group cp-card flex min-h-56 flex-col gap-5 p-6 transition duration-200 hover:-translate-y-1 hover:shadow-lift focus-visible:ring-2 focus-visible:ring-sand-600">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sand-100 text-sand-800 transition group-hover:bg-sand-200" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6">
                                    <path d="M7 3.75h7l4 4v12.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" />
                                    <path d="M14 3.75v4h4M8.5 12h7M8.5 15.5h7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                </svg>
                            </span>
                            <span class="flex flex-1 flex-col gap-2">
                                <span class="text-lg font-bold text-slate-900">Devoirs publiés</span>
                                <span class="text-sm leading-6 text-slate-600">Retrouve les consignes et les dates de tes enseignants.</span>
                            </span>
                            <span class="inline-flex items-center gap-2 text-sm font-semibold text-sand-800">
                                Consulter les devoirs
                                <span aria-hidden="true" class="transition group-hover:translate-x-1">→</span>
                            </span>
                        </a>

                        <a href="{{ route('calendrier.index') }}"
                           class="group cp-card flex min-h-56 flex-col gap-5 p-6 transition duration-200 hover:-translate-y-1 hover:shadow-lift focus-visible:ring-2 focus-visible:ring-coral-600">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-coral-100 text-coral-800 transition group-hover:bg-coral-200" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6">
                                    <path d="M7 3v3m10-3v3M4.5 9h15M6 5h12a1.5 1.5 0 0 1 1.5 1.5v12A1.5 1.5 0 0 1 18 20H6a1.5 1.5 0 0 1-1.5-1.5v-12A1.5 1.5 0 0 1 6 5Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                </svg>
                            </span>
                            <span class="flex flex-1 flex-col gap-2">
                                <span class="text-lg font-bold text-slate-900">Calendrier et rappels</span>
                                <span class="text-sm leading-6 text-slate-600">Vois les échéances qui arrivent et programme un rappel.</span>
                            </span>
                            <span class="inline-flex items-center gap-2 text-sm font-semibold text-coral-800">
                                Ouvrir le calendrier
                                <span aria-hidden="true" class="transition group-hover:translate-x-1">→</span>
                            </span>
                        </a>
                    </div>
                </section>
            @elseif (auth()->user()->role === \App\Models\User::ROLE_TEACHER)
                <section class="grid gap-5 lg:grid-cols-[1.2fr_0.8fr]" aria-labelledby="actions-enseignant">
                    <a href="{{ route('devoirs.index') }}"
                       class="group cp-card flex flex-col gap-5 p-7 transition duration-200 hover:-translate-y-1 hover:shadow-lift focus-visible:ring-2 focus-visible:ring-emerald-600 sm:p-8">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-800" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6">
                                <path d="M7 3.75h7l4 4v12.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" />
                                <path d="M14 3.75v4h4M8.5 12h7M8.5 15.5h7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                            </svg>
                        </span>
                        <span class="flex flex-col gap-2">
                            <span id="actions-enseignant" class="text-xl font-bold text-slate-900">Mes devoirs</span>
                            <span class="max-w-lg text-sm leading-6 text-slate-600">Prépare tes consignes, choisis les étudiants concernés et garde une vue claire sur les échéances.</span>
                        </span>
                        <span class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-800">
                            Gérer mes devoirs <span aria-hidden="true" class="transition group-hover:translate-x-1">→</span>
                        </span>
                    </a>
                    <aside class="flex flex-col justify-between gap-6 rounded-4xl border border-coral-100 bg-gradient-to-br from-coral-50 to-white p-7 sm:p-8">
                        <div class="flex flex-col gap-3">
                            <span class="cp-eyebrow text-coral-700">Une nouvelle consigne ?</span>
                            <h2 class="text-lg font-bold text-slate-900">Partage-la avec les bons étudiants.</h2>
                            <p class="text-sm leading-6 text-slate-600">Tu peux publier pour toute la plateforme, une filière ou une classe.</p>
                        </div>
                        <a href="{{ route('devoirs.create') }}" class="cp-button-primary w-full">
                            Créer un devoir
                        </a>
                    </aside>
                </section>
            @else
                <section class="grid gap-4 sm:grid-cols-2" aria-label="Outils d’administration">
                    <a href="{{ route('admin.utilisateurs.index') }}"
                       class="group cp-card flex items-start gap-4 p-6 transition hover:-translate-y-1 hover:shadow-lift">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 font-bold text-emerald-800" aria-hidden="true">01</span>
                        <span>
                            <span class="block font-bold text-slate-900">Comptes utilisateurs</span>
                            <span class="mt-1 block text-sm leading-6 text-slate-600">Recherche, consultation et état des accès.</span>
                        </span>
                    </a>
                    <a href="{{ route('admin.filieres.index') }}"
                       class="group cp-card flex items-start gap-4 p-6 transition hover:-translate-y-1 hover:shadow-lift">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-sand-100 font-bold text-sand-800" aria-hidden="true">02</span>
                        <span>
                            <span class="block font-bold text-slate-900">Classes et filières</span>
                            <span class="mt-1 block text-sm leading-6 text-slate-600">Organisation des classes et rattachement des étudiants.</span>
                        </span>
                    </a>
                </section>
            @endif

            <footer class="flex flex-col gap-1 border-t border-slate-200/80 pt-5 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
                <span>ClassPilot</span>
                <span>Avance à ton rythme, une étape à la fois.</span>
            </footer>
        </div>
    </div>
</x-app-layout>
