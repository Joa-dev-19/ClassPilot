<x-app-layout>
    <div class="min-h-screen bg-slate-50 px-5 py-8 sm:px-8">
        <main class="mx-auto max-w-5xl">
            @if (session('success'))
                <div class="mb-6 rounded-xl bg-green-100 p-4 text-green-800" role="status">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-6 rounded-xl bg-red-50 p-4 text-red-800" role="alert">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <header class="mb-8 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Mes devoirs</h1>
                    <p class="mt-2 text-slate-600">Crée, publie et archive tes devoirs.</p>
                </div>
                <a href="{{ route('devoirs.create') }}"
                   class="rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                    Nouveau devoir
                </a>
            </header>

            @if ($devoirs->isEmpty())
                <section class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-800">Aucun devoir pour le moment</h2>
                    <p class="mt-2 text-slate-600">Les devoirs que tu crées apparaîtront ici.</p>
                </section>
            @else
                <div class="flex flex-col gap-4">
                    @foreach ($devoirs as $devoir)
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <h2 class="font-semibold text-slate-900">
                                        <a href="{{ route('devoirs.show', $devoir) }}"
                                           class="rounded-sm hover:text-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
                                            {{ $devoir->titre }}
                                        </a>
                                    </h2>
                                    <p class="mt-2 text-sm text-slate-600">
                                        Date limite : {{ $devoir->date_limite->format('d/m/Y') }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-600">
                                        Destinataires :
                                        {{ $devoir->classe
                                            ? $devoir->classe->nom.' · '.$devoir->classe->filiere->nom
                                            : ($devoir->filiere?->nom ?? 'Tous les étudiants') }}
                                    </p>
                                </div>
                                <span @class([
                                    'rounded-full px-3 py-1 text-sm font-semibold',
                                    'bg-amber-100 text-amber-800' => $devoir->statut === \App\Models\Devoir::STATUS_DRAFT,
                                    'bg-green-100 text-green-800' => $devoir->statut === \App\Models\Devoir::STATUS_PUBLISHED,
                                    'bg-slate-100 text-slate-700' => $devoir->statut === \App\Models\Devoir::STATUS_ARCHIVED,
                                ])>
                                    {{ match ($devoir->statut) {
                                        \App\Models\Devoir::STATUS_DRAFT => 'Brouillon',
                                        \App\Models\Devoir::STATUS_PUBLISHED => 'Publié',
                                        default => 'Archivé',
                                    } }}
                                </span>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-3">
                                <a href="{{ route('devoirs.show', $devoir) }}"
                                   class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                                    Consulter
                                </a>
                                @if ($devoir->statut !== \App\Models\Devoir::STATUS_ARCHIVED)
                                    <a href="{{ route('devoirs.edit', $devoir) }}"
                                       class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                                        Modifier
                                    </a>
                                @endif
                                @if ($devoir->statut === \App\Models\Devoir::STATUS_DRAFT)
                                    <form action="{{ route('devoirs.publish', $devoir) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="rounded-lg bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-700 focus-visible:ring-offset-2">
                                            Publier
                                        </button>
                                    </form>
                                @endif
                                @if ($devoir->statut !== \App\Models\Devoir::STATUS_ARCHIVED)
                                    <form action="{{ route('devoirs.archive', $devoir) }}" method="POST"
                                          onsubmit="return confirm('Archiver ce devoir ?')">
                                        @csrf
                                        <button type="submit"
                                                class="rounded-lg border border-red-200 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">
                                            Archiver
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="mt-6">{{ $devoirs->links() }}</div>
            @endif
        </main>
    </div>
</x-app-layout>
