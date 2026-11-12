<x-app-layout>
    <div class="min-h-screen bg-slate-50 px-5 py-8 sm:px-8">
        <main class="mx-auto max-w-5xl">
            <header class="mb-8">
                <h1 class="text-2xl font-bold text-slate-900">Devoirs publiés</h1>
                <p class="mt-2 text-slate-600">Les devoirs visibles ici sont destinés à tous les étudiants ou à ta classe.</p>
            </header>

            @if ($devoirs->isEmpty())
                <section class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-800">Aucun devoir publié</h2>
                    <p class="mt-2 text-slate-600">Les nouveaux devoirs apparaîtront ici après leur publication.</p>
                </section>
            @else
                <div class="flex flex-col gap-4">
                    @foreach ($devoirs as $devoir)
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div>
                                    <h2 class="font-semibold text-slate-900">
                                        <a href="{{ route('devoirs.etudiants.show', $devoir) }}"
                                           class="rounded-sm hover:text-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
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
                                   class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                                    Consulter
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="mt-6">{{ $devoirs->links() }}</div>
            @endif
        </main>
    </div>
</x-app-layout>
