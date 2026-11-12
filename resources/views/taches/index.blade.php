<x-app-layout>
    <div class="cp-page">
        <div class="cp-page-inner flex max-w-5xl flex-col gap-7">
            @if (session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900" role="status">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="cp-heading text-3xl">
                        Mes tâches
                    </h1>

                    <p class="mt-2 text-sm text-slate-600 sm:text-base">
                        Organise ton travail et suis tes échéances.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('dashboard') }}"
                       class="cp-button-secondary w-full text-sm sm:w-auto">
                        Tableau de bord
                    </a>
                    <a href="{{ route('calendrier.index') }}"
                       class="cp-button-secondary w-full text-sm sm:w-auto">
                        Calendrier
                    </a>
                    <a href="{{ route('taches.create') }}"
                       class="cp-button-primary w-full text-sm sm:w-auto">
                        + Nouvelle tâche
                    </a>
                </div>
            </div>

            @if ($taches->isEmpty())
                <div class="cp-card flex flex-col items-center gap-2 p-8 text-center">
                    <h2 class="text-lg font-semibold text-slate-800">
                        Aucune tâche pour le moment
                    </h2>

                    <p class="mt-2 text-slate-500">
                        Tes tâches apparaîtront ici lorsque tu en auras créé.
                    </p>
                </div>
            @else
                <div class="flex flex-col gap-4">
                    @foreach ($taches as $tache)
                        <article class="cp-card flex flex-col gap-5 p-5 sm:p-6">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex flex-col gap-2">
                                    <h2 class="text-lg font-bold text-slate-900">
                                        {{ $tache->titre }}
                                    </h2>

                                    @if ($tache->description)
                                        <p class="max-w-2xl text-sm leading-6 text-slate-600">
                                            {{ $tache->description }}
                                        </p>
                                    @endif

                                    <p class="text-sm text-slate-500">
                                        Date limite :
                                        {{ $tache->date_limite
                                            ? \Carbon\Carbon::parse($tache->date_limite)->format('d/m/Y')
                                            : 'Non définie' }}
                                    </p>
                                </div>

                                <div class="flex flex-wrap gap-2 text-xs font-semibold">
                                    <p class="rounded-full bg-sand-100 px-3 py-1.5 text-sand-900">
                                        Priorité ·
                                        {{ match ($tache->priorite) {
                                            'faible' => 'Faible',
                                            'moyenne' => 'Moyenne',
                                            'elevee' => 'Élevée',
                                            default => 'Non définie',
                                        } }}
                                    </p>
                                    <p @class([
                                        'rounded-full px-3 py-1.5',
                                        'bg-emerald-100 text-emerald-900' => $tache->statut === 'terminee',
                                        'bg-coral-100 text-coral-900' => $tache->statut === 'en_cours',
                                        'bg-slate-100 text-slate-800' => $tache->statut === 'a_faire',
                                    ])>
                                        Statut ·
                                        {{ match ($tache->statut) {
                                            'a_faire' => 'À faire',
                                            'en_cours' => 'En cours',
                                            'terminee' => 'Terminée',
                                            default => 'Non défini',
                                        } }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row">
                                <a href="{{ route('taches.edit', $tache) }}"
                                   class="cp-button-secondary w-full text-sm sm:w-auto">
                                    Modifier
                                </a>

                                <form action="{{ route('taches.destroy', $tache) }}" method="POST"
                                      onsubmit="return confirm('Supprimer cette tâche ?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="w-full rounded-xl border border-red-200 bg-white px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-50 focus-visible:ring-2 focus-visible:ring-red-600 sm:w-auto">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>