<x-app-layout>
    <div class="min-h-screen bg-slate-50 px-5 py-8 sm:px-8">
        <div class="mx-auto max-w-5xl">
            @if (session('success'))
                <div class="mb-6 rounded-xl bg-green-100 p-4 text-green-800" role="status">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">
                        Mes tâches
                    </h1>

                    <p class="mt-2 text-slate-500">
                        Organise ton travail et suis tes échéances.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('dashboard') }}"
                       class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                        Tableau de bord
                    </a>
                    <a href="{{ route('calendrier.index') }}"
                       class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                        Calendrier
                    </a>
                    <a href="{{ route('taches.create') }}"
                       class="rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                        + Nouvelle tâche
                    </a>
                </div>
            </div>

            @if ($taches->isEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
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
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <h2 class="font-semibold text-slate-900">
                                        {{ $tache->titre }}
                                    </h2>

                                    @if ($tache->description)
                                        <p class="mt-2 text-sm text-slate-600">
                                            {{ $tache->description }}
                                        </p>
                                    @endif

                                    <p class="mt-3 text-sm text-slate-500">
                                        Date limite :
                                        {{ $tache->date_limite
                                            ? \Carbon\Carbon::parse($tache->date_limite)->format('d/m/Y')
                                            : 'Non définie' }}
                                    </p>
                                </div>

                                <div class="text-sm text-slate-600">
                                    <p>
                                        Priorité :
                                        {{ match ($tache->priorite) {
                                            'faible' => 'Faible',
                                            'moyenne' => 'Moyenne',
                                            'elevee' => 'Élevée',
                                            default => 'Non définie',
                                        } }}
                                    </p>
                                    <p class="mt-1">
                                        Statut :
                                        {{ match ($tache->statut) {
                                            'a_faire' => 'À faire',
                                            'en_cours' => 'En cours',
                                            'terminee' => 'Terminée',
                                            default => 'Non défini',
                                        } }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-3">
                                <a href="{{ route('taches.edit', $tache) }}"
                                   class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                                    Modifier
                                </a>

                                <form action="{{ route('taches.destroy', $tache) }}" method="POST"
                                      onsubmit="return confirm('Supprimer cette tâche ?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="rounded-lg border border-red-200 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">
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