<x-app-layout>
    <div class="cp-page">
        <div class="cp-page-inner flex max-w-5xl flex-col gap-7">
            @if (session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900" role="status">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800" role="alert">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="cp-heading text-3xl">Mes devoirs</h1>
                    <p class="mt-2 text-sm text-slate-600 sm:text-base">Crée, publie et archive tes devoirs.</p>
                </div>
                <a href="{{ route('devoirs.create') }}"
                   class="cp-button-primary w-full text-sm sm:w-auto">
                    Nouveau devoir
                </a>
            </header>

            @if ($devoirs->isEmpty())
                <section class="cp-card flex flex-col items-center gap-2 p-8 text-center">
                    <h2 class="text-lg font-semibold text-slate-800">Aucun devoir pour le moment</h2>
                    <p class="mt-2 text-slate-600">Les devoirs que tu crées apparaîtront ici.</p>
                </section>
            @else
                <div class="flex flex-col gap-4">
                    @foreach ($devoirs as $devoir)
                        <article class="cp-card flex flex-col gap-5 p-5 sm:p-6">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex flex-col gap-1">
                                    <h2 class="text-lg font-bold text-slate-900">
                                        <a href="{{ route('devoirs.show', $devoir) }}"
                                           class="rounded-sm transition hover:text-emerald-800 focus-visible:ring-2 focus-visible:ring-emerald-600">
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
                                    'w-fit rounded-full px-3 py-1 text-sm font-semibold',
                                    'bg-sand-100 text-sand-900' => $devoir->statut === \App\Models\Devoir::STATUS_DRAFT,
                                    'bg-emerald-100 text-emerald-900' => $devoir->statut === \App\Models\Devoir::STATUS_PUBLISHED,
                                    'bg-slate-100 text-slate-700' => $devoir->statut === \App\Models\Devoir::STATUS_ARCHIVED,
                                ])>
                                    {{ match ($devoir->statut) {
                                        \App\Models\Devoir::STATUS_DRAFT => 'Brouillon',
                                        \App\Models\Devoir::STATUS_PUBLISHED => 'Publié',
                                        default => 'Archivé',
                                    } }}
                                </span>
                            </div>

                            <div class="flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:flex-wrap">
                                <a href="{{ route('devoirs.show', $devoir) }}"
                                   class="cp-button-secondary w-full text-sm sm:w-auto">
                                    Consulter
                                </a>
                                @if ($devoir->statut !== \App\Models\Devoir::STATUS_ARCHIVED)
                                    <a href="{{ route('devoirs.edit', $devoir) }}"
                                       class="cp-button-secondary w-full text-sm sm:w-auto">
                                        Modifier
                                    </a>
                                @endif
                                @if ($devoir->statut === \App\Models\Devoir::STATUS_DRAFT)
                                    <form action="{{ route('devoirs.publish', $devoir) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="cp-button-primary w-full text-sm sm:w-auto">
                                            Publier
                                        </button>
                                    </form>
                                @endif
                                @if ($devoir->statut !== \App\Models\Devoir::STATUS_ARCHIVED)
                                    <form action="{{ route('devoirs.archive', $devoir) }}" method="POST"
                                          onsubmit="return confirm('Archiver ce devoir ?')">
                                        @csrf
                                        <button type="submit"
                                                class="w-full rounded-xl border border-red-200 bg-white px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-50 focus-visible:ring-2 focus-visible:ring-red-600 sm:w-auto">
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
        </div>
    </div>
</x-app-layout>
