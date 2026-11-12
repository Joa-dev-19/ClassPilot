<x-app-layout>
    <div class="cp-page">
        <div class="cp-page-inner flex max-w-5xl flex-col gap-7">
            <header class="flex flex-col gap-2">
                <h1 class="cp-heading text-3xl">Devoirs publiés</h1>
                <p class="mt-2 text-slate-600">Les devoirs visibles ici sont destinés à tous les étudiants ou à ta classe.</p>
            </header>

            @if ($devoirs->isEmpty())
                <section class="cp-card flex flex-col items-center gap-2 p-8 text-center">
                    <h2 class="text-lg font-semibold text-slate-800">Aucun devoir publié</h2>
                    <p class="mt-2 text-slate-600">Les nouveaux devoirs apparaîtront ici après leur publication.</p>
                </section>
            @else
                <div class="flex flex-col gap-4">
                    @foreach ($devoirs as $devoir)
                        <article class="cp-card flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                                <div class="flex flex-col gap-1">
                                    <h2 class="text-lg font-bold text-slate-900">
                                        <a href="{{ route('devoirs.etudiants.show', $devoir) }}"
                                           class="rounded-sm transition hover:text-emerald-800 focus-visible:ring-2 focus-visible:ring-emerald-600">
                                            {{ $devoir->titre }}
                                        </a>
                                    </h2>
                                    <p class="mt-2 text-sm text-slate-600">
                                        Date limite : {{ $devoir->date_limite->format('d/m/Y') }}
                                    </p>
                                    @if ($devoir->classe)
                                        <p class="mt-1 text-sm text-slate-600">
                                            Classe : {{ $devoir->classe->nom }} · {{ $devoir->classe->filiere->nom }}
                                        </p>
                                    @elseif ($devoir->filiere)
                                        <p class="mt-1 text-sm text-slate-600">
                                            Filière : {{ $devoir->filiere->nom }}
                                        </p>
                                    @endif
                                </div>
                                <a href="{{ route('devoirs.etudiants.show', $devoir) }}"
                                   class="cp-button-secondary w-full text-sm sm:w-auto">
                                    Consulter
                                </a>
                        </article>
                    @endforeach
                </div>
                <div class="mt-6">{{ $devoirs->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
